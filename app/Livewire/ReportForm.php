<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Report;
use App\Models\Family;
use App\Models\WeeklyMeeting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Carbon\Carbon;

#[Layout('layouts.app')]
class ReportForm extends Component
{
    public $reportId;
    public $type;
    public $family;
    public $isReadOnly = false;

    // --- Weekly Report Data ---
    public $timeline = [];
    public $weekly_achievements = [];
    public $visitation_hours;

    // --- Monthly Report Data ---
    public $report_date_input;
    public $monthly_summary = [];
    public $members_notes = [];
    public $stats_snapshot = [];
    public $members_monthly_stats = [];

    // --- Common ---
    public $priest_message = [];
    public $admin_general_reply;
    public $admin_reply_at;

    public $familyMembers = [];

    public function mount($type = null, Report $report = null)
    {
        $this->report_date_input = Carbon::now()->format('Y-m');

        if ($report && $report->id) {
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->family_id !== $report->family_id) {
                abort(403);
            }

            $this->reportId = $report->id;
            $this->type = $report->type;
            $this->family = $report->family;
            $this->isReadOnly = true;

            // Load & Normalize Data (Convert old single reply to replies array)
            $this->timeline = $this->normalizeSection($report->timeline ?? [['time' => '', 'activity' => '']]);
            $this->weekly_achievements = $this->normalizeSection($report->weekly_achievements ?? [['text' => '']]);
            $this->visitation_hours = $report->visitation_hours;
            $this->monthly_summary = $this->normalizeSection($report->monthly_summary ?? [['text' => '']]);
            $this->stats_snapshot = $report->stats_snapshot ?? [];
            $this->report_date_input = Carbon::parse($report->report_date)->format('Y-m');
            $this->priest_message = $this->normalizeSection($report->priest_message ?? [['text' => '']]);
            $this->members_notes = $this->normalizeMembersNotes($report->members_notes ?? []);

            $this->admin_general_reply = $report->admin_reply;
            $this->admin_reply_at = $report->admin_reply_at;
        } else {
            $this->type = $type;
            $this->family = Auth::user()->family;

            // Initialize empty structures for new report
            $this->timeline = $this->normalizeSection([['time' => '', 'activity' => '']]);
            $this->weekly_achievements = $this->normalizeSection([['text' => '']]);
            $this->monthly_summary = $this->normalizeSection([['text' => '']]);
            $this->priest_message = $this->normalizeSection([['text' => '']]);
        }

        if ($this->family) {
            $this->familyMembers = $this->family->members()->get();

            if ($this->isReadOnly) {
                foreach ($this->familyMembers as $member) {
                    if (!isset($this->members_notes[$member->id])) {
                        $this->members_notes[$member->id] = ['text' => '', 'replies' => [], 'new_reply' => ''];
                    }
                }
            }
        }

        if ($this->type == 'monthly' && $this->family) {
            $this->calculateMonthlyStats();
            if (!$this->isReadOnly) {
                $this->initMembersNotes();
            }
        }
    }

    /**
     * Helper to upgrade old DB structure to new threaded replies structure
     */
    private function normalizeSection($section)
    {
        if (!is_array($section)) return [];
        foreach ($section as &$item) {
            if (!isset($item['replies'])) {
                $item['replies'] = [];
                // Migrate old single reply if exists
                if (isset($item['reply']) && $item['reply'] !== '') {
                    $item['replies'][] = [
                        'role' => 'admin',
                        'name' => 'الإدارة',
                        'text' => $item['reply'],
                        'date' => now()->toDateTimeString()
                    ];
                }
            }
            unset($item['reply']);
            $item['new_reply'] = ''; // Input field for new reply
        }
        return $section;
    }

    private function normalizeMembersNotes($notes)
    {
        if (!is_array($notes)) return [];
        foreach ($notes as $id => &$note) {
            if (is_string($note)) {
                $note = ['text' => $note, 'replies' => [], 'new_reply' => ''];
            } else {
                if (!isset($note['replies'])) {
                    $note['replies'] = [];
                    if (isset($note['reply']) && $note['reply'] !== '') {
                        $note['replies'][] = [
                            'role' => 'admin',
                            'name' => 'الإدارة',
                            'text' => $note['reply'],
                            'date' => now()->toDateTimeString()
                        ];
                    }
                }
                unset($note['reply']);
                $note['new_reply'] = '';
            }
        }
        return $notes;
    }

    public function updatedReportDateInput()
    {
        if ($this->type == 'monthly') {
            $this->calculateMonthlyStats();
        }
    }

    public function addItem($listName)
    {
        if (in_array($listName, ['weekly_achievements', 'monthly_summary', 'priest_message'])) {
            $this->$listName[] = ['text' => '', 'replies' => [], 'new_reply' => ''];
        }
    }

    public function removeItem($listName, $index)
    {
        if (in_array($listName, ['weekly_achievements', 'monthly_summary', 'priest_message'])) {
            unset($this->$listName[$index]);
            $this->$listName = array_values($this->$listName);
        }
    }

    public function initMembersNotes()
    {
        if ($this->family) {
            $members = $this->family->members()->where('is_active', true)->get();
            foreach ($members as $member) {
                if (!isset($this->members_notes[$member->id])) {
                    $this->members_notes[$member->id] = ['text' => '', 'replies' => [], 'new_reply' => ''];
                }
            }
        }
    }

    public function addTimelineRow()
    {
        $this->timeline[] = ['time' => '', 'activity' => '', 'replies' => [], 'new_reply' => ''];
    }

    public function removeTimelineRow($index)
    {
        unset($this->timeline[$index]);
        $this->timeline = array_values($this->timeline);
    }

    public function calculateMonthlyStats()
    {
        if (!$this->family) return;

        try {
            $date = Carbon::parse($this->report_date_input);
        } catch (\Exception $e) {
            $date = Carbon::now();
        }

        $meetings = WeeklyMeeting::where('family_id', $this->family->id)
            ->where('status', 'completed')
            ->whereMonth('week_date', $date->month)
            ->whereYear('week_date', $date->year)
            ->with('tatmimRecords')
            ->get();

        $meetingsCount = $meetings->count();
        $this->members_monthly_stats = [];

        if ($meetingsCount == 0) {
            $this->stats_snapshot = ['status' => 'no_data', 'month_name' => $date->locale('ar')->monthName];
            return;
        }

        $globalSums = [
            'attendance' => 0, 'note' => 0, 'kholwa' => 0, 'training' => 0,
            'weekly_kholwa' => 0, 'mass' => 0, 'vespers' => 0, 'tasbeha' => 0,
            'sermon' => 0, 'servants' => 0, 'reading' => 0, 'altar' => 0,
        ];

        $totalPresentCount = 0;

        foreach ($this->familyMembers as $member) {
            $memberStats = [
                'name' => $member->name, 'is_active' => $member->is_active, 'attendance' => 0,
                'note_score' => 0, 'has_mass' => 0, 'has_servants_meeting' => 0,
                'has_tasbeha' => 0, 'has_reading' => 0, 'has_sermon' => 0,
                'has_family_altar' => 0, 'kholwa_count' => 0, 'talmaza_training_count' => 0,
                'has_weekly_kholwa' => 0,
            ];

            foreach ($meetings as $meeting) {
                $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();

                if ($record) {
                    if ($record->is_present) {
                        $memberStats['attendance']++;
                        $totalPresentCount++;
                        $globalSums['attendance']++;
                    }

                    $maxNote = max($meeting->max_note_score, 1);
                    $memberStats['note_score'] += ($record->note_score / $maxNote);
                    if ($record->is_present) $globalSums['note'] += ($record->note_score / $maxNote);

                    if ($record->has_mass) { $memberStats['has_mass']++; if ($record->is_present) $globalSums['mass']++; }
                    if ($record->has_servants_meeting) { $memberStats['has_servants_meeting']++; if ($record->is_present) $globalSums['servants']++; }
                    if ($record->has_vespers || $record->has_tasbeha) { $memberStats['has_tasbeha']++; if ($record->is_present) $globalSums['vespers']++; }
                    if ($record->has_reading) { $memberStats['has_reading']++; if ($record->is_present) $globalSums['reading']++; }
                    if ($record->has_family_altar) { $memberStats['has_family_altar']++; if ($record->is_present) $globalSums['altar']++; }
                    if ($record->has_weekly_kholwa) { $memberStats['has_weekly_kholwa']++; if ($record->is_present) $globalSums['weekly_kholwa']++; }
                    if ($record->has_sermon) { $memberStats['has_sermon']++; if ($record->is_present) $globalSums['sermon']++; }

                    $memberStats['kholwa_count'] += min($record->kholwa_count / 7, 1);
                    if ($record->kholwa_count > 3 && $record->is_present) $globalSums['kholwa']++;

                    $memberStats['talmaza_training_count'] += min($record->talmaza_training_count / 7, 1);
                    if ($record->talmaza_training_count > 3 && $record->is_present) $globalSums['training']++;
                }
            }

            foreach ($memberStats as $key => $val) {
                if (in_array($key, ['name', 'is_active'])) continue;
                $memberStats[$key] = round(($val / $meetingsCount) * 100);
            }
            $this->members_monthly_stats[] = $memberStats;
        }

        $activeMembersCount = $this->family->members()->where('is_active', true)->count();
        $totalOpp = $meetingsCount * max($activeMembersCount, 1);

        $avgs = [];
        $avgs['attendance'] = round(($globalSums['attendance'] / $totalOpp) * 100);
        $avgs['note'] = $totalPresentCount > 0 ? round(($globalSums['note'] / $totalPresentCount) * 100) : 0;

        $keys = ['kholwa', 'training', 'weekly_kholwa', 'mass', 'vespers', 'servants', 'reading', 'altar', 'tasbeha' ,'sermon'];
        foreach ($keys as $k) {
            $srcKey = $k == 'vespers' ? 'vespers' : $k;
            $avgs[$k] = round(($globalSums[$srcKey] / $totalOpp) * 100);
        }

        $this->stats_snapshot = array_merge([
            'month_name' => $date->locale('ar')->monthName,
            'meetings_count' => $meetingsCount
        ], $avgs);
    }

    public function save()
    {
        $this->weekly_achievements = array_values(array_filter($this->weekly_achievements, fn($i) => !empty($i['text'])));
        $this->monthly_summary = array_values(array_filter($this->monthly_summary, fn($i) => !empty($i['text'])));
        $this->priest_message = array_values(array_filter($this->priest_message, fn($i) => !empty($i['text'])));

        $this->validate([
            'visitation_hours' => $this->type == 'weekly' ? 'nullable|numeric|min:0' : 'nullable',
        ]);

        $newReport = Report::create([
            'family_id' => $this->family->id,
            'type' => $this->type,
            'report_date' => $this->type == 'monthly' ? Carbon::parse($this->report_date_input)->endOfMonth() : now(),
            'timeline' => $this->type == 'weekly' ? $this->timeline : null,
            'weekly_achievements' => $this->type == 'weekly' ? $this->weekly_achievements : null,
            'visitation_hours' => $this->type == 'weekly' ? $this->visitation_hours : null,
            'monthly_summary' => $this->type == 'monthly' ? $this->monthly_summary : null,
            'members_notes' => $this->type == 'monthly' ? $this->members_notes : null,
            'stats_snapshot' => $this->type == 'monthly' ? $this->stats_snapshot : null,
            'priest_message' => $this->priest_message,
        ]);

        try {
            $tokens = array_unique(User::where('role', 'admin')
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray());

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();

                $userName = Auth::user()->name;
                $reportTypeAr = $this->type == 'weekly' ? 'الأسبوعي' : 'الشهري';
                $reportUrl = route('report.view', $newReport->id, true);

                $message = CloudMessage::new()
                    ->withNotification(Notification::create(
                        'تقرير جديد 📝',
                        "تم ارسال تقرير {$reportTypeAr} بواسطة {$userName}."
                    ))
                    ->withWebPushConfig(WebPushConfig::fromArray([
                        'fcm_options' => ['link' => $reportUrl]
                    ]));

                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) {
            Log::error('Firebase Notification Error (New Report): ' . $e->getMessage());
        }

        return redirect()->route('leader.reports')->with('message', 'تم إرسال التقرير بنجاح ✅');
    }

    /**
     * This function is now used by BOTH Admin and Leader to save their replies
     */
    public function saveReplies()
    {
        $report = Report::find($this->reportId);
        if (!$report) return;

        $hasNewReplies = false;
        $userRole = Auth::user()->role;
        $userName = Auth::user()->name;

        // Loop through all sections and append any text in 'new_reply' to the 'replies' array
        $sections = ['timeline', 'weekly_achievements', 'monthly_summary', 'priest_message'];
        foreach ($sections as $section) {
            if (is_array($this->$section)) {
                foreach ($this->$section as &$item) {
                    if (!empty($item['new_reply'])) {
                        $item['replies'][] = [
                            'role' => $userRole,
                            'name' => $userName,
                            'text' => $item['new_reply'],
                            'date' => now()->toDateTimeString()
                        ];
                        $item['new_reply'] = ''; // Clear input after appending
                        $hasNewReplies = true;
                    }
                }
            }
        }

        // Do the same for members_notes
        if (is_array($this->members_notes)) {
            foreach ($this->members_notes as $memberId => &$note) {
                if (!empty($note['new_reply'])) {
                    $note['replies'][] = [
                        'role' => $userRole,
                        'name' => $userName,
                        'text' => $note['new_reply'],
                        'date' => now()->toDateTimeString()
                    ];
                    $note['new_reply'] = '';
                    $hasNewReplies = true;
                }
            }
        }

        if ($hasNewReplies) {
            $report->update([
                'timeline' => $this->type == 'weekly' ? $this->timeline : null,
                'weekly_achievements' => $this->type == 'weekly' ? $this->weekly_achievements : null,
                'monthly_summary' => $this->type == 'monthly' ? $this->monthly_summary : null,
                'priest_message' => $this->priest_message,
                'members_notes' => $this->type == 'monthly' ? $this->members_notes : null,
                // Only update admin_reply_at if the replier is an admin
                'admin_reply_at' => $userRole === 'admin' ? now() : $report->admin_reply_at,
            ]);

            // Notify Logic
            try {
                $tokens = [];
                $title = '';
                $body = '';

                if ($userRole === 'admin') {
                    // Notify Leader(s) of this family
                    $tokens = User::where('family_id', $report->family_id)
                        ->whereNotNull('fcm_token')
                        ->where('fcm_token', '!=', '')
                        ->pluck('fcm_token')
                        ->toArray();

                    $title = 'رد جديد على التقرير 📝';
                    $body = "تم الرد على تقريرك بواسطة {$userName}.";
                } else {
                    // Notify Admins
                    $tokens = User::where('role', 'admin')
                        ->whereNotNull('fcm_token')
                        ->where('fcm_token', '!=', '')
                        ->pluck('fcm_token')
                        ->toArray();

                    $title = 'تعقيب جديد من القائد 📝';
                    $body = "قام {$userName} بالرد على ملاحظات التقرير.";
                }

                $tokens = array_unique($tokens);

                if (!empty($tokens)) {
                    $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                    $messaging = $factory->createMessaging();
                    $reportUrl = route('report.view', $report->id, true);

                    $message = CloudMessage::new()
                        ->withNotification(Notification::create($title, $body))
                        ->withWebPushConfig(WebPushConfig::fromArray([
                            'fcm_options' => ['link' => $reportUrl]
                        ]));

                    $messaging->sendMulticast($message, $tokens);
                }
            } catch (\Throwable $e) {
                Log::error('Firebase Notification Error (Reply): ' . $e->getMessage());
            }
        }

        session()->flash('message', 'تم حفظ الردود بنجاح ✅');
    }

    public function render()
    {
        return view('livewire.report-form');
    }
}
