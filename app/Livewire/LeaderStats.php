<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WeeklyMeeting;
use App\Services\TatmimStatsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class LeaderStats extends Component
{
    public $month;
    public $year;
    public $availableYears = [];

    public function mount()
    {
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;

        // Generate last 3 years
        $currentYear = Carbon::now()->year;
        $this->availableYears = range($currentYear, $currentYear - 2);
    }

    public function calculateStats(TatmimStatsService $statsService)
    {
        $family = Auth::user()->family;
        if (!$family) return null;

        $meetings = WeeklyMeeting::where('family_id', $family->id)
            ->where('status', 'completed')
            ->whereMonth('week_date', $this->month)
            ->whereYear('week_date', $this->year)
            ->with('tatmimRecords')
            ->get();

        $meetingsCount = $meetings->count();
        if ($meetingsCount == 0) return null;

        $membersStats = [];
        $members = $family->members()->where('is_active', true)->get();

        foreach ($members as $member) {
            // Using the service ensures the math is identical to Admin view
            $stats = $statsService->calculateMemberStats($member, $meetings);
            $membersStats[] = $stats;
        }

        // Sort by Total Average
        usort($membersStats, function ($a, $b) {
            return $b['total_average'] <=> $a['total_average'];
        });

        // Calculate Family Averages
        $familyAverages = [];
        $activeMembersCount = count($membersStats);

        if ($activeMembersCount > 0) {
            // Get the keys to average from the service's empty counters plus the total average
            $keys = array_keys($statsService->calculateRecordStats(null, $meetings->first()));
            $keys[] = 'total_average';

            foreach ($keys as $key) {
                $sum = array_sum(array_column($membersStats, $key));
                $familyAverages[$key] = round($sum / $activeMembersCount);
            }
        }

        return [
            'meetings_count' => $meetingsCount,
            'members_stats' => $membersStats,
            'family_averages' => $familyAverages
        ];
    }

    public function render(TatmimStatsService $statsService)
    {
        return view('livewire.leader-stats', [
            'data' => $this->calculateStats($statsService)
        ]);
    }
}
