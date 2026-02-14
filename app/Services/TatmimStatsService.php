<?php

namespace App\Services;

use App\Models\TatmimRecord;
use App\Models\WeeklyMeeting;

class TatmimStatsService
{
    /**
     * Calculate aggregated stats (Average %) for a member over a collection of meetings.
     */
    public function calculateMemberStats($member, $meetings)
    {
        // 1. Initialize counters
        $counters = $this->getEmptyCounters();

        $meetingsCount = $meetings->count();
        if ($meetingsCount === 0) {
            return $this->formatStats($member, $counters, 0);
        }

        // 2. Iterate and sum up the percentages
        foreach ($meetings as $meeting) {
            $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();

            // Calculate scores for this specific meeting (0 to 100)
            $scores = $this->calculateRecordStats($record, $meeting);

            // Add the percentage to the total sum
            foreach ($scores as $key => $score) {
                if (isset($counters[$key])) {
                    $counters[$key] += $score;
                }
            }
        }

        return $this->formatStats($member, $counters, $meetingsCount);
    }

    /**
     * Calculate normalized scores (0-100) for a SINGLE meeting/record.
     */
    public function calculateRecordStats($record, $meeting)
    {
        if (!$record) {
            return array_fill_keys(array_keys($this->getEmptyCounters()), 0);
        }

        $maxNote = max($meeting->max_note_score, 1); // Prevent division by zero

        return [
            // Boolean Fields (0 or 100)
            'attendance' => $record->is_present ? 100 : 0,
            'mass' => $record->has_mass ? 100 : 0,
            // Check both columns if vespers was renamed or keep single if cleaned up
            'tasbeha' => ($record->has_vespers ?? false || $record->has_tasbeha) ? 100 : 0,
            'servants' => $record->has_servants_meeting ? 100 : 0,
            'reading' => $record->has_reading ? 100 : 0,
            'altar' => $record->has_family_altar ? 100 : 0,
            'weekly_kholwa' => $record->has_weekly_kholwa ? 100 : 0,

            // Gradient Fields (Calculated Percentage 0-100)
            'note' => ($record->note_score / $maxNote) * 100,
            'kholwa' => min(($record->kholwa_count / 7) * 100, 100), // Cap at 100%
            'training' => min(($record->talmaza_training_count / 7) * 100, 100), // Cap at 100%
        ];
    }

    private function getEmptyCounters()
    {
        return [
            'attendance' => 0,
            'note' => 0,
            'kholwa' => 0,
            'training' => 0,
            'weekly_kholwa' => 0,
            'mass' => 0,
            'tasbeha' => 0,
            'servants' => 0,
            'reading' => 0,
            'altar' => 0,
        ];
    }

    private function formatStats($member, $counters, $totalCount)
    {
        $stats = [];
        $totalSum = 0;
        $metricsCount = count($counters);

        foreach ($counters as $key => $val) {
            // Since $val is a sum of percentages (e.g., 250 for 3 meetings),
            // We just divide by count to get the average (250 / 3 = 83%)
            $avg = $totalCount > 0 ? round($val / $totalCount) : 0;
            $stats[$key] = $avg;
            $totalSum += $avg;
        }

        return array_merge($stats, [
            'id' => $member->id,
            'name' => $member->name,
            'is_active' => $member->is_active,
            'total_average' => $metricsCount > 0 ? round($totalSum / $metricsCount) : 0
        ]);
    }
}
