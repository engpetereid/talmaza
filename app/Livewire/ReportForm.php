<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Report;
use App\Models\Family;
use App\Models\WeeklyMeeting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class ReportForm extends Component
{
    public $reportId;
    public $type;
    public $family;
    public $isReadOnly = false;

    // --- Weekly Report Data ---
    public $timeline = [['time' => '', 'activity' => '', 'reply' => '']];
    public $weekly_achievements = [['text' => '', 'reply' => '']];
    public $visitation_hours;

    // --- Monthly Report Data ---
    public $report_date_input;
    public $monthly_summary = [['text' => '', 'reply' => '']];
    public $members_notes = [];
    public $stats_snapshot = [];
    public $members_monthly_stats = [];

    // --- Common ---
    public $priest_message = [['text' => '', 'reply' => '']];
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

            // Load Data
            $this->timeline = $report->timeline ?? [['time' => '', 'activity' => '', 'reply' => '']];
            $this->weekly_achievements = $report->weekly_achievements ?? [['text' => '', 'reply' => '']];
            $this->visitation_hours = $report->visitation_hours;
            $this->monthly_summary = $report->monthly_summary ?? [['text' => '', 'reply' => '']];
            $this->stats_snapshot = $report->stats_snapshot ?? [];
            $this->report_date_input = Carbon::parse($report->report_date)->format('Y-m');
            $this->priest_message = $report->priest_message ?? [['text' => '', 'reply' => '']];
            $this->members_notes = $report->members_notes ?? [];
            $this->admin_general_reply = $report->admin_reply;
            $this->admin_reply_at = $report->admin_reply_at;
        } else {
            $this->type = $type;
            $this->family = Auth::user()->family;
        }

        if ($this->family) {
            $this->familyMembers = $this->family->members()->get();

            if ($this->isReadOnly) {
                foreach ($this->familyMembers as $member) {
                    if (!isset($this->members_notes[$member->id])) {
                        $this->members_notes[$member->id] = ['text' => '', 'reply' => ''];
                    } elseif (is_string($this->members_notes[$member->id])) {
                        $this->members_notes[$member->id] = ['text' => $this->members_notes[$member->id], 'reply' => ''];
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

    public function updatedReportDateInput()
    {
        if ($this->type == 'monthly') {
            $this->calculateMonthlyStats();
        }
    }

    public function addItem($listName)
    {
        if (in_array($listName, ['weekly_achievements', 'monthly_summary', 'priest_message'])) {
            $this->$listName[] = ['text' => '', 'reply' => ''];
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
                    $this->members_notes[$member->id] = ['text' => '', 'reply' => ''];
                }
            }
        }
    }

    public function addTimelineRow()
    {
        $this->timeline[] = ['time' => '', 'activity' => '', 'reply' => ''];
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
            'attendance' => 0,
            'note' => 0,
            'kholwa' => 0,
            'training' => 0,
            'weekly_kholwa' => 0,
            'mass' => 0,
            'vespers' => 0,
            'tasbeha' => 0,
            'servants' => 0,
            'reading' => 0,
            'altar' => 0,
        ];

        $totalPresentCount = 0;

        foreach ($this->familyMembers as $member) {
            $memberStats = [
                'name' => $member->name,
                'is_active' => $member->is_active,
                'attendance' => 0,
                'note_score' => 0,
                'has_mass' => 0,
                'has_servants_meeting' => 0,
                'has_tasbeha' => 0,
                'has_reading' => 0,
                'has_family_altar' => 0,
                'kholwa_count' => 0,
                'talmaza_training_count' => 0,
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
                    if ($record->is_present) {
                        $globalSums['note'] += ($record->note_score / $maxNote);
                    }

                    if ($record->has_mass) {
                        $memberStats['has_mass']++;
                        if ($record->is_present) $globalSums['mass']++;
                    }
                    if ($record->has_servants_meeting) {
                        $memberStats['has_servants_meeting']++;
                        if ($record->is_present) $globalSums['servants']++;
                    }
                    if ($record->has_vespers || $record->has_tasbeha) {
                        $memberStats['has_tasbeha']++;
                        if ($record->is_present) $globalSums['vespers']++;
                    }
                    if ($record->has_reading) {
                        $memberStats['has_reading']++;
                        if ($record->is_present) $globalSums['reading']++;
                    }
                    if ($record->has_family_altar) {
                        $memberStats['has_family_altar']++;
                        if ($record->is_present) $globalSums['altar']++;
                    }
                    if ($record->has_weekly_kholwa) {
                        $memberStats['has_weekly_kholwa']++;
                        if ($record->is_present) $globalSums['weekly_kholwa']++;
                    }

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

        $keys = ['kholwa', 'training', 'weekly_kholwa', 'mass', 'vespers', 'servants', 'reading', 'altar'];
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

        Report::create([
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

        return redirect()->route('leader.reports')->with('message', 'تم إرسال التقرير بنجاح ✅');
    }

    public function saveAdminReply()
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $report = Report::find($this->reportId);

        $report->update([
            'timeline' => $this->type == 'weekly' ? $this->timeline : null, // Added timeline update
            'weekly_achievements' => $this->type == 'weekly' ? $this->weekly_achievements : null,
            'monthly_summary' => $this->type == 'monthly' ? $this->monthly_summary : null,
            'priest_message' => $this->priest_message,
            'members_notes' => $this->members_notes,
            'admin_reply' => $this->admin_general_reply,
            'admin_reply_at' => now(),
        ]);

        session()->flash('message', 'تم حفظ الردود بنجاح ✅');
    }

    public function render()
    {
        return view('livewire.report-form');
    }
}
