<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Family;
use App\Models\WeeklyMeeting;
use App\Services\TatmimStatsService;
use Carbon\Carbon;

#[Layout('layouts.app')]
class AdminFamilyStats extends Component
{
    public Family $family;
    public $month;
    public $year;
    public $availableYears = [];
    public $showMeetingsList = false;

    // Configuration for charts
    public $metricsConfig = [
        'attendance' => ['label' => 'حضور', 'color' => '#3b82f6'],
        'note' => ['label' => 'نوتة', 'color' => '#a855f7'],
        'mass' => ['label' => 'قداس', 'color' => '#ea580c'],
        'kholwa' => ['label' => 'مشاركة خلوة', 'color' => '#f97316'],
        'training' => ['label' => 'تلمذة', 'color' => '#ec4899'],
        'vespers' => ['label' => 'عشية/تسبحة', 'color' => '#4f46e5'],
        'servants' => ['label' => 'اجتماع خدام', 'color' => '#16a34a'],
        'reading' => ['label' => 'قراءة', 'color' => '#0d9488'],
        'altar' => ['label' => 'مذبح', 'color' => '#dc2626'],
        'weekly_kholwa' => ['label' => 'خلوة أسبوعية', 'color' => '#be185d'],
    ];

    public function mount(Family $family)
    {
        $this->family = $family;
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;

        // Generate available years (Current year + 2 previous years)
        $currentYear = Carbon::now()->year;
        $this->availableYears = range($currentYear, $currentYear - 2);
    }

    public function toggleMeetingsList()
    {
        $this->showMeetingsList = !$this->showMeetingsList;
    }

    public function calculateStats(TatmimStatsService $statsService)
    {
        $meetings = WeeklyMeeting::where('family_id', $this->family->id)
            ->where('status', 'completed')
            ->whereMonth('week_date', $this->month)
            ->whereYear('week_date', $this->year)
            ->with('tatmimRecords')
            ->orderBy('week_date')
            ->get();

        $meetingsCount = $meetings->count();
        if ($meetingsCount == 0) return null;

        $members = $this->family->members;
        $membersStats = [];

        // --- 1. Prepare Chart Data (Weekly Trends) ---
        // Initialize arrays for all metrics
        $chartData = ['labels' => []];
        foreach (array_keys($this->metricsConfig) as $key) {
            $chartData[$key] = [];
        }

        foreach ($meetings as $meeting) {
            $chartData['labels'][] = Carbon::parse($meeting->week_date)->format('d/m');

            // Initialize sums for this meeting
            $meetingSums = array_fill_keys(array_keys($this->metricsConfig), 0);
            $activeMembersCount = 0;

            foreach ($members as $member) {
                if (!$member->is_active) continue;
                $activeMembersCount++;

                $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();
                if ($record) {
                    $maxNote = max($meeting->max_note_score, 1);

                    // Boolean Metrics (Add 100 if true)
                    if ($record->is_present) $meetingSums['attendance'] += 100;
                    if ($record->has_mass) $meetingSums['mass'] += 100;
                    if ($record->has_weekly_kholwa) $meetingSums['weekly_kholwa'] += 100;
                    if ($record->has_tasbeha || $record->has_vespers) $meetingSums['vespers'] += 100;
                    if ($record->has_servants_meeting) $meetingSums['servants'] += 100;
                    if ($record->has_reading) $meetingSums['reading'] += 100;
                    if ($record->has_family_altar) $meetingSums['altar'] += 100;

                    // Gradient/Calculated Metrics
                    $meetingSums['note'] += ($record->note_score / $maxNote) * 100;
                    $meetingSums['kholwa'] += min(($record->kholwa_count / 7) * 100, 100);
                    $meetingSums['training'] += min(($record->talmaza_training_count / 7) * 100, 100);
                }
            }

            // Calculate Averages for this meeting
            foreach (array_keys($this->metricsConfig) as $key) {
                $chartData[$key][] = $activeMembersCount > 0 ? round($meetingSums[$key] / $activeMembersCount) : 0;
            }
        }

        // --- 2. Calculate Member Stats (Monthly Average) ---
        foreach ($members as $member) {
            $counters = array_fill_keys(array_keys($this->metricsConfig), 0);

            foreach ($meetings as $meeting) {
                $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();

                if ($record) {
                    // Accumulate scores for average calculation
                    $maxNote = max($meeting->max_note_score, 1);

                    if ($record->is_present) $counters['attendance'] += 100;
                    if ($record->has_mass) $counters['mass'] += 100;
                    if ($record->has_weekly_kholwa) $counters['weekly_kholwa'] += 100;
                    if ($record->has_tasbeha || $record->has_vespers) $counters['vespers'] += 100;
                    if ($record->has_servants_meeting) $counters['servants'] += 100;
                    if ($record->has_reading) $counters['reading'] += 100;
                    if ($record->has_family_altar) $counters['altar'] += 100;

                    $counters['note'] += ($record->note_score / $maxNote) * 100;
                    $counters['kholwa'] += min(($record->kholwa_count / 7) * 100, 100);
                    $counters['training'] += min(($record->talmaza_training_count / 7) * 100, 100);
                }
            }

            // Final Stats
            $stats = [];
            $totalSum = 0;
            foreach ($counters as $key => $val) {
                $avg = round($val / $meetingsCount);
                $stats[$key] = $avg;
                $totalSum += $avg;
            }

            $stats['id'] = $member->id; // ADDED ID
            $stats['name'] = $member->name;
            $stats['is_active'] = $member->is_active;
            $stats['total_average'] = round($totalSum / count($counters));

            $membersStats[] = $stats;
        }

        usort($membersStats, function ($a, $b) {
            return $b['total_average'] <=> $a['total_average'];
        });

        return [
            'meetings_count' => $meetingsCount,
            'members_stats' => $membersStats,
            'chart_data' => $chartData,
            'meetings' => $meetings
        ];
    }

    public function render(TatmimStatsService $statsService)
    {
        return view('livewire.admin-family-stats', [
            'data' => $this->calculateStats($statsService)
        ]);
    }
}
