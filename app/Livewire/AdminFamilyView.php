<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Family;
use App\Models\Member;
use App\Models\Report;
use App\Models\WeeklyMeeting; // Import WeeklyMeeting model

#[Layout('layouts.app')]
class AdminFamilyView extends Component
{
    public Family $family;

    public function mount(Family $family)
    {
        $this->family = $family;
    }

    public function deleteMember($memberId)
    {
        $member = Member::find($memberId);
        if ($member) {
            // If member has records, deactivate instead of delete to preserve history
            if ($member->tatmimRecords()->count() > 0) {
                $member->update(['is_active' => false]);
                session()->flash('error', 'لا يمكن حذف عضو له سجلات سابقة، تم تحويله لغير نشط بدلاً من ذلك.');
            } else {
                $member->delete();
                session()->flash('status', 'تم حذف العضو بنجاح.');
            }
        }
    }

    public function deleteFamily()
    {
        $this->family->delete();

        return redirect()->route('admin.dashboard')->with('message', 'تم حذف العائلة وجميع بياناتها بنجاح 🗑️');
    }

    public function render()
    {
        return view('livewire.admin-family-view', [
            'members' => $this->family->members()->orderBy('is_active', 'desc')->get(),

            // Fetch last 4 meetings
            'meetings' => WeeklyMeeting::where('family_id', $this->family->id)
                ->latest('week_date')
                ->take(4)
                ->get(),

            // Fetch last 10 reports
            'reports' => Report::where('family_id', $this->family->id)
                ->latest('report_date')
                ->take(10)
                ->get()
        ]);
    }
}
