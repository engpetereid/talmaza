<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\WeeklyMeeting;
use App\Models\Announcement;
use Carbon\Carbon;

#[Layout('layouts.app')]
class LeaderDashboard extends Component
{
    public $family;
    public $currentMeeting;
    public $previousMeetings = [];

    public function mount()
    {
        $user = Auth::user();
        $this->family = $user->family;

        if ($this->family) {
            // 1. Current Meeting (Latest by date)
            $this->currentMeeting = WeeklyMeeting::where('family_id', $this->family->id)
                ->latest('week_date')
                ->first();

            // 2. Previous Meetings (History)
            $this->previousMeetings = WeeklyMeeting::where('family_id', $this->family->id)
                ->when($this->currentMeeting, function ($query) {
                    $query->where('id', '!=', $this->currentMeeting->id);
                })
                ->latest('week_date')
                ->take(5)
                ->get();
        } else {
            $this->currentMeeting = null;
        }
    }

    public function createTestMeeting()
    {
        if (!$this->family) return;

        WeeklyMeeting::create([
            'family_id' => $this->family->id,
            'week_date' => now(),
            'status' => 'pending',
            'max_note_score' => 100,
        ]);

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.leader-dashboard', [
            'latestPost' => Announcement::latest()->first()
        ]);
    }
}
