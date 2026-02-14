<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Family;
use App\Models\Member;
use App\Models\WeeklyMeeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

#[Layout('layouts.app')]
class AdminDashboard extends Component
{
    public $search = ''; //  البحث


    public function render()
    {
        // الإحصائيات العامة
        $stats = [
            'families_count' => Family::count(),
            'members_count' => Member::where('is_active', true)->count(),
            'active_meetings_this_week' => WeeklyMeeting::where('status', 'completed')
                ->whereBetween('week_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count()
        ];

        //  قائمة العائلات (مع البحث)
        $familiesQuery = Family::with(['users', 'members'])
            ->with(['weeklyMeetings' => function ($query) {
                $query->where('status', 'completed')->latest('week_date');
            }])
            ->withCount('members');

        // لو فيه بحث، نفلتر بالاسم أو اسم القائد
        if (!empty($this->search)) {
            $familiesQuery->where('name', 'like', '%' . $this->search . '%')
                ->orWhereHas('users', function ($q) {
                    $q->where('role', 'leader')->where('name', 'like', '%' . $this->search . '%');
                });
        }

        $families = $familiesQuery->get()->map(function ($family) {
            $lastMeeting = $family->weeklyMeetings->first();

            // تحديد عنوان الدرس الأخير (سواء كان منهج أو موضوع حر)
            $lastLessonTitle = 'لم يبدأ بعد';
            if ($lastMeeting) {
                $lastLessonTitle = $lastMeeting->lesson->title ?? $lastMeeting->custom_topic ?? 'درس بدون عنوان';
            }

            return [
                'id' => $family->id,
                'name' => $family->name,
                'leader_name' => $family->users->where('role', 'leader')->first()->name ?? 'بدون قائد',
                'members_count' => $family->members_count,
                'last_lesson' => $lastLessonTitle,
                'last_meeting_date' => $lastMeeting
                    ? Carbon::parse($lastMeeting->week_date)->locale('ar')->diffForHumans()
                    : '-',
                'stage_name' => $lastMeeting && $lastMeeting->lesson && $lastMeeting->lesson->stage
                    ? $lastMeeting->lesson->stage->name
                    : ($lastMeeting && $lastMeeting->custom_topic ? 'موضوع اخر' : 'جديد'),
            ];
        });

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'families' => $families
        ]);
    }
}
