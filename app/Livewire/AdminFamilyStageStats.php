<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\WeeklyMeeting;
use App\Models\Stage;
use App\Models\Family;
use App\Services\TatmimStatsService;
use Carbon\Carbon;

#[Layout('layouts.app')]
class AdminFamilyStageStats extends Component
{
    public Family $family;
    public $selectedStageId;

    // UI State
    public $showMeetingsList = false;

    public function mount(Family $family)
    {
        $this->family = $family;

        // Default to first stage
        $firstStage = Stage::orderBy('order_number')->first();
        $this->selectedStageId = $firstStage ? $firstStage->id : null;
    }

    public function toggleMeetingsList()
    {
        $this->showMeetingsList = !$this->showMeetingsList;
    }

    public function calculateStats(TatmimStatsService $statsService)
    {
        if (!$this->selectedStageId) return null;

        $meetingsQuery = WeeklyMeeting::where('family_id', $this->family->id)
            ->where('status', 'completed')
            ->whereHas('lesson', function($q) {
                $q->where('stage_id', $this->selectedStageId);
            })
            ->with('tatmimRecords')
            ->orderBy('week_date');

        $meetings = $meetingsQuery->get();
        $meetingsCount = $meetings->count();

        if ($meetingsCount == 0) return null;

        $members = $this->family->members;
        $membersStats = [];

        // ---  Prepare Chart Data ---
        $chartData = [
            'labels' => [],
            'attendance' => [],
            'note' => [],
            'mass' => [],
        ];

        foreach ($meetings as $meeting) {
            $chartData['labels'][] = Carbon::parse($meeting->week_date)->format('d/m');

            $meetingAttendance = 0;
            $meetingNote = 0;
            $meetingMass = 0;
            $activeMembersCount = 0;

            foreach ($members as $member) {
                if (!$member->is_active) continue;
                $activeMembersCount++;

                $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();
                if ($record) {
                    // Manually handle gradient logic for charts if service returns binary
                    if ($record->is_present) $meetingAttendance++;

                    $maxNote = max($meeting->max_note_score, 1);
                    $meetingNote += ($record->note_score / $maxNote) * 100;

                    if ($record->has_mass) $meetingMass += 100;
                }
            }

            $chartData['attendance'][] = $activeMembersCount > 0 ? round(($meetingAttendance / $activeMembersCount) * 100) : 0;
            $chartData['note'][] = $activeMembersCount > 0 ? round($meetingNote / $activeMembersCount) : 0;
            $chartData['mass'][] = $activeMembersCount > 0 ? round($meetingMass / $activeMembersCount) : 0;
        }

        // --- Calculate Member Stats ---
        foreach ($members as $member) {
            // Use Service to calculate stats for the collection
            $stats = $statsService->calculateMemberStats($member, $meetings);
            $membersStats[] = $stats;
        }

        // Sort by Performance
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
        return view('livewire.admin-family-stage-stats', [
            'stages' => Stage::orderBy('order_number')->get(),
            'data' => $this->calculateStats($statsService)
        ]);
    }
}
