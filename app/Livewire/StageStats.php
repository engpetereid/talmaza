<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WeeklyMeeting;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use App\Services\TatmimStatsService; 

#[Layout('layouts.app')]
class StageStats extends Component
{
    public $selectedStageId;

    public function mount()
    {
        $firstStage = Stage::orderBy('order_number')->first();
        $this->selectedStageId = $firstStage ? $firstStage->id : null;
    }

    public function calculateStats(TatmimStatsService $statsService)
    {
        $family = Auth::user()->family;
        if (!$family || !$this->selectedStageId) return null;

        $meetings = WeeklyMeeting::where('family_id', $family->id)
            ->where('status', 'completed')
            ->whereHas('lesson', function($q) {
                $q->where('stage_id', $this->selectedStageId);
            })
            ->with('tatmimRecords')
            ->get();

        $meetingsCount = $meetings->count();
        if ($meetingsCount == 0) return null;

        $membersStats = [];
        $members = $family->members()->where('is_active', true)->get();

        foreach ($members as $member) {
            // Use the service for consistent logic
            $stats = $statsService->calculateMemberStats($member, $meetings);
            $membersStats[] = $stats;
        }

        // Sort by Performance
        usort($membersStats, function ($a, $b) {
            return $b['total_average'] <=> $a['total_average'];
        });

        return [
            'meetings_count' => $meetingsCount,
            'members_stats' => $membersStats
        ];
    }

    public function render(TatmimStatsService $statsService)
    {
        return view('livewire.stage-stats', [
            'stages' => Stage::orderBy('order_number')->get(),
            'data' => $this->calculateStats($statsService)
        ]);
    }
}
