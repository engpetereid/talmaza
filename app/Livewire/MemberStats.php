<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Member;
use App\Models\WeeklyMeeting;
use App\Services\TatmimStatsService;
use Carbon\Carbon;

#[Layout('layouts.app')]
class MemberStats extends Component
{
    public Member $member;

    public $isEditing = false;
    public $name;
    public $phone;
    public $birth_date;
    public $job_or_college;
    public $confession_father;
    public $talents;

    public $period = 12;

    public function mount(Member $member)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->family_id !== $member->family_id) {
            abort(403);
        }

        $this->member = $member;
        $this->name = $member->name;
        $this->phone = $member->phone;
        $this->birth_date = $member->birth_date ? $member->birth_date->format('Y-m-d') : null;
        $this->job_or_college = $member->job_or_college;
        $this->confession_father = $member->confession_father;
        $this->talents = $member->talents;
    }

    public function setPeriod($weeks)
    {
        $this->period = $weeks;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->mount($this->member);
        }
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'phone' => 'nullable|string|max:15',
            'birth_date' => 'nullable|date',
            'job_or_college' => 'nullable|string|max:255',
            'confession_father' => 'nullable|string|max:255',
            'talents' => 'nullable|string|max:500',
        ]);

        $this->member->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date ?: null,
            'job_or_college' => $this->job_or_college,
            'confession_father' => $this->confession_father,
            'talents' => $this->talents,
        ]);

        $this->isEditing = false;
        session()->flash('message', 'تم تحديث البيانات بنجاح ✅');
    }

    public function render(TatmimStatsService $statsService)
    {
        $meetings = WeeklyMeeting::where('family_id', $this->member->family_id)
            ->where('status', 'completed')
            ->latest('week_date')
            ->take($this->period)
            ->with(['tatmimRecords' => function ($q) {
                $q->where('member_id', $this->member->id);
            }])
            ->get()
            ->reverse();

        $meetingsCount = $meetings->count();

        $chartLabels = $meetings->pluck('week_date')->map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        })->values()->toArray();

        $metrics = [
            'attendance' => ['label' => 'الحضور', 'color' => '#2563eb', 'total' => 0, 'trend' => []],
            'note' => ['label' => 'النوتة', 'color' => '#9333ea', 'total' => 0, 'trend' => []],
            'mass' => ['label' => 'القداس', 'color' => '#ea580c', 'total' => 0, 'trend' => []],
            'vespers' => ['label' => 'عشية/تسبحة', 'color' => '#4f46e5', 'total' => 0, 'trend' => []],
            'servants' => ['label' => 'اجتماع خدام', 'color' => '#059669', 'total' => 0, 'trend' => []],
            'reading' => ['label' => 'القراءة', 'color' => '#0d9488', 'total' => 0, 'trend' => []],
            'altar' => ['label' => 'مذبح عائلي', 'color' => '#be185d', 'total' => 0, 'trend' => []],
            'weekly_kholwa' => ['label' => 'خلوة أسبوعية', 'color' => '#db2777', 'total' => 0, 'trend' => []],
            'kholwa' => ['label' => 'مشاركة خلوة', 'color' => '#d97706', 'total' => 0, 'trend' => []],
            'training' => ['label' => 'تدريب تلمذة', 'color' => '#ca8a04', 'total' => 0, 'trend' => []],
        ];

        $history = [];

        foreach ($meetings as $meeting) {
            $record = $meeting->tatmimRecords->first();

            // Initialize with zeros
            $calculatedScores = array_fill_keys(array_keys($metrics), 0);

            if ($record) {
                // 1. Boolean Metrics (Strict Check)
                $calculatedScores['attendance'] = $record->is_present ? 100 : 0;
                $calculatedScores['mass'] = $record->has_mass ? 100 : 0;
                // Combine Vespers/Tasbeha if they are separate columns in DB
                $calculatedScores['vespers'] = ($record->has_tasbeha || $record->has_vespers) ? 100 : 0;
                $calculatedScores['servants'] = $record->has_servants_meeting ? 100 : 0;
                $calculatedScores['reading'] = $record->has_reading ? 100 : 0;
                $calculatedScores['altar'] = $record->has_family_altar ? 100 : 0;
                $calculatedScores['weekly_kholwa'] = $record->has_weekly_kholwa ? 100 : 0;

                // 2. Gradient Metrics (Calculated)
                $maxNote = max($meeting->max_note_score, 1);
                $calculatedScores['note'] = ($record->note_score / $maxNote) * 100;
                $calculatedScores['kholwa'] = min(($record->kholwa_count / 7) * 100, 100);
                $calculatedScores['training'] = min(($record->talmaza_training_count / 7) * 100, 100);
            }

            foreach ($metrics as $key => &$data) {
                $score = $calculatedScores[$key];
                $data['total'] += $score;
                $data['trend'][] = $score;
            }

            array_unshift($history, [
                'date' => $meeting->week_date,
                'present' => $record ? $record->is_present : false,
                'note' => $record ? $record->note_score : 0,
                'max_note' => $meeting->max_note_score
            ]);
        }

        foreach ($metrics as $key => &$data) {
            $data['average'] = $meetingsCount > 0 ? round($data['total'] / $meetingsCount) : 0;
        }

        return view('livewire.member-stats', [
            'metrics' => $metrics,
            'history' => $history,
            'chartLabels' => $chartLabels,
            'meetingsCount' => $meetingsCount
        ]);
    }
}
