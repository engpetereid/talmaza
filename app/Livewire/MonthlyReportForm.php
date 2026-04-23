<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Report;
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
class MonthlyReportForm extends Component
{
    public $reportId;
    public $family;
    public $isReadOnly = false;

    public $report_date_input;
    public $monthly_summary = [];
    public $members_notes = [];

    // --- Stats Data ---
    public $stats_snapshot = [];
    public $members_monthly_stats = [];
    public $stats_replies = []; // 👈 الردود الخاصة بقسم الإحصائيات
    public $stats_new_reply = ''; // 👈 الرد الجديد للإحصائيات

    public $priest_message = [];
    public $familyMembers = [];

    public function mount(Report $report = null)
    {
        $this->report_date_input = Carbon::now()->format('Y-m');

        if ($report && $report->id) {
            $user = Auth::user();
            if ($user->role !== 'admin' && $user->family_id !== $report->family_id) { abort(403); }

            $this->reportId = $report->id;
            $this->family = $report->family;
            $this->isReadOnly = true;

            $this->monthly_summary = $this->normalizeSection($report->monthly_summary ?? [['text' => '']]);
            $this->stats_snapshot = $report->stats_snapshot ?? [];
            $this->stats_replies = is_array($report->stats_replies) ? $report->stats_replies : []; // 👈 استرجاع ردود الإحصائيات
            $this->report_date_input = Carbon::parse($report->report_date)->format('Y-m');
            $this->priest_message = $this->normalizeSection($report->priest_message ?? [['text' => '']]);
            $this->members_notes = $this->normalizeMembersNotes($report->members_notes ?? []);
        } else {
            $this->family = Auth::user()->family;
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

        if ($this->family) {
            $this->calculateMonthlyStats();
            if (!$this->isReadOnly) { $this->initMembersNotes(); }
        }
    }

    private function normalizeSection($section)
    {
        if (!is_array($section)) return [];
        foreach ($section as &$item) {
            if (!isset($item['replies'])) {
                $item['replies'] = [];
                if (isset($item['reply']) && $item['reply'] !== '') {
                    $item['replies'][] = ['role' => 'admin', 'name' => 'الإدارة', 'text' => $item['reply'], 'date' => now()->toDateTimeString()];
                }
            }
            unset($item['reply']);
            $item['new_reply'] = '';
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
                        $note['replies'][] = ['role' => 'admin', 'name' => 'الإدارة', 'text' => $note['reply'], 'date' => now()->toDateTimeString()];
                    }
                }
                unset($note['reply']);
                $note['new_reply'] = '';
            }
        }
        return $notes;
    }

    public function updatedReportDateInput() { $this->calculateMonthlyStats(); }

    public function addItem($listName) { $this->$listName[] = ['text' => '', 'replies' => [], 'new_reply' => '']; }
    public function removeItem($listName, $index) { unset($this->$listName[$index]); $this->$listName = array_values($this->$listName); }

    public function initMembersNotes()
    {
        $members = $this->family->members()->where('is_active', true)->get();
        foreach ($members as $member) {
            if (!isset($this->members_notes[$member->id])) {
                $this->members_notes[$member->id] = ['text' => '', 'replies' => [], 'new_reply' => ''];
            }
        }
    }

    public function calculateMonthlyStats()
    {
        try { $date = Carbon::parse($this->report_date_input); } catch (\Exception $e) { $date = Carbon::now(); }

        $meetings = WeeklyMeeting::where('family_id', $this->family->id)
            ->where('status', 'completed')
            ->whereMonth('week_date', $date->month)
            ->whereYear('week_date', $date->year)
            ->with('tatmimRecords')->get();

        $meetingsCount = $meetings->count();
        $this->members_monthly_stats = [];

        if ($meetingsCount == 0) {
            $this->stats_snapshot = ['status' => 'no_data', 'month_name' => $date->locale('ar')->monthName];
            return;
        }

        $globalSums = array_fill_keys(['attendance', 'note', 'kholwa', 'training', 'weekly_kholwa', 'mass', 'vespers', 'tasbeha', 'sermon', 'servants', 'reading', 'altar'], 0);
        $totalPresentCount = 0;

        foreach ($this->familyMembers as $member) {
            $memberStats = array_fill_keys(array_keys($globalSums), 0);
            $memberStats['name'] = $member->name;
            $memberStats['is_active'] = $member->is_active;
            $memberStats['note_score'] = 0;
            $memberStats['kholwa_count'] = 0;
            $memberStats['talmaza_training_count'] = 0;

            foreach ($meetings as $meeting) {
                $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();
                if ($record) {
                    if ($record->is_present) { $memberStats['attendance']++; $totalPresentCount++; $globalSums['attendance']++; }

                    $maxNote = max($meeting->max_note_score, 1);
                    $memberStats['note_score'] += ($record->note_score / $maxNote);
                    if ($record->is_present) $globalSums['note'] += ($record->note_score / $maxNote);

                    if ($record->has_mass) { $memberStats['mass']++; $globalSums['mass']++; }
                    if ($record->has_servants_meeting) { $memberStats['servants']++; $globalSums['servants']++; }
                    if ($record->has_tasbeha) { $memberStats['tasbeha']++; $globalSums['tasbeha']++; }
                    if ($record->has_vespers) { $memberStats['vespers']++; $globalSums['vespers']++; }
                    if ($record->has_reading) { $memberStats['reading']++; $globalSums['reading']++; }
                    if ($record->has_family_altar) { $memberStats['altar']++; $globalSums['altar']++; }
                    if ($record->has_weekly_kholwa) { $memberStats['weekly_kholwa']++; $globalSums['weekly_kholwa']++; }
                    if ($record->has_sermon) { $memberStats['sermon']++; $globalSums['sermon']++; }

                    $kholwaFrac = min($record->kholwa_count / 7, 1);
                    $memberStats['kholwa_count'] += $kholwaFrac;
                    $globalSums['kholwa'] += $kholwaFrac;

                    $trainFrac = min($record->talmaza_training_count / 7, 1);
                    $memberStats['talmaza_training_count'] += $trainFrac;
                    $globalSums['training'] += $trainFrac;
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
        foreach (['kholwa', 'training', 'weekly_kholwa', 'mass', 'vespers', 'servants', 'reading', 'altar', 'tasbeha', 'sermon'] as $k) {
            $avgs[$k] = round(($globalSums[$k] / $totalOpp) * 100);
        }

        $this->stats_snapshot = array_merge(['month_name' => $date->locale('ar')->monthName, 'meetings_count' => $meetingsCount], $avgs);
    }

    public function save()
    {
        $this->monthly_summary = array_values(array_filter($this->monthly_summary, fn($i) => !empty($i['text'])));
        $this->priest_message = array_values(array_filter($this->priest_message, fn($i) => !empty($i['text'])));

        $newReport = Report::create([
            'family_id' => $this->family->id,
            'type' => 'monthly',
            'report_date' => Carbon::parse($this->report_date_input)->endOfMonth(),
            'monthly_summary' => $this->monthly_summary,
            'members_notes' => $this->members_notes,
            'stats_snapshot' => $this->stats_snapshot,
            'stats_replies' => [], // 👈 إضافة حقل ردود الإحصائيات
            'priest_message' => $this->priest_message,
        ]);

        $this->notifyUsers($newReport, 'تقرير جديد 📝', "تم ارسال التقرير الشهري بواسطة " . Auth::user()->name);

        return redirect()->route('leader.reports')->with('message', 'تم إرسال التقرير بنجاح ✅');
    }

    public function saveReplies()
    {
        $report = Report::find($this->reportId);
        if (!$report) return;

        $hasNewReplies = false;
        $userRole = Auth::user()->role;
        $userName = Auth::user()->name;

        // ردود الأقسام العادية
        foreach (['monthly_summary', 'priest_message'] as $section) {
            if (is_array($this->$section)) {
                foreach ($this->$section as &$item) {
                    if (!empty($item['new_reply'])) {
                        $item['replies'][] = ['role' => $userRole, 'name' => $userName, 'text' => $item['new_reply'], 'date' => now()->toDateTimeString()];
                        $item['new_reply'] = '';
                        $hasNewReplies = true;
                    }
                }
            }
        }

        // ردود المخدومين
        if (is_array($this->members_notes)) {
            foreach ($this->members_notes as &$note) {
                if (!empty($note['new_reply'])) {
                    $note['replies'][] = ['role' => $userRole, 'name' => $userName, 'text' => $note['new_reply'], 'date' => now()->toDateTimeString()];
                    $note['new_reply'] = '';
                    $hasNewReplies = true;
                }
            }
        }

        // 👈 الرد على قسم الإحصائيات الشاملة
        if (!empty($this->stats_new_reply)) {
            $this->stats_replies[] = ['role' => $userRole, 'name' => $userName, 'text' => $this->stats_new_reply, 'date' => now()->toDateTimeString()];
            $this->stats_new_reply = '';
            $hasNewReplies = true;
        }

        if ($hasNewReplies) {
            $report->update([
                'monthly_summary' => $this->monthly_summary,
                'members_notes' => $this->members_notes,
                'stats_replies' => $this->stats_replies, // 👈 حفظ ردود الإحصائيات في DB
                'priest_message' => $this->priest_message,
                'admin_reply_at' => $userRole === 'admin' ? now() : $report->admin_reply_at,
            ]);

            $title = $userRole === 'admin' ? 'رد جديد على التقرير 📝' : 'تعقيب جديد من القائد 📝';
            $body = $userRole === 'admin' ? "تم الرد على تقريرك بواسطة {$userName}." : "قام {$userName} بالرد على ملاحظات التقرير.";
            $this->notifyUsers($report, $title, $body, true);
        }
        session()->flash('message', 'تم حفظ الردود بنجاح ✅');
    }

    public function closeReplies()
    {
        $report = Report::find($this->reportId);
        $report->update(['admin_reply_at' => Auth::user()->role === 'admin' ? now() : $report->admin_reply_at]);
        session()->flash('message', 'تم الاغلاق بنجاح ✅');
    }

    private function notifyUsers($report, $title, $body, $isReply = false)
    {
        try {
            $targetRole = (Auth::user()->role === 'admin' && $isReply) ? 'leader' : 'admin';
            $query = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '');
            if ($targetRole === 'admin') { $query->where('role', 'admin'); } else { $query->where('family_id', $report->family_id); }

            $tokens = array_unique($query->pluck('fcm_token')->toArray());

            if (!empty($tokens)) {
                $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-auth.json'));
                $messaging = $factory->createMessaging();
                $message = CloudMessage::new()->withNotification(Notification::create($title, $body))
                    ->withWebPushConfig(WebPushConfig::fromArray(['fcm_options' => ['link' => route('report.monthly.view', $report->id)]]));
                $messaging->sendMulticast($message, $tokens);
            }
        } catch (\Throwable $e) { Log::error('Firebase Error: ' . $e->getMessage()); }
    }

    public function render()
    {
        return view('livewire.monthly-report-form');
    }
}
