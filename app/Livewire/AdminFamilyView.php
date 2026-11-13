<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Family;
use App\Models\Member;
use App\Models\Report;
use App\Models\User;
use App\Models\WeeklyMeeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        // Only members of the family being viewed
        $member = $this->family->members()->find($memberId);
        if ($member) {
            // If member has records, deactivate instead of delete to preserve history
            if ($member->tatmimRecords()->count() > 0) {
                $member->update(['is_active' => false]);
                session()->flash('error', 'لا يمكن حذف عضو له سجلات سابقة، تم تحويله لغير نشط بدلاً من ذلك.');
            } else {
                if ($member->photo_path) {
                    Storage::disk('public')->delete($member->photo_path);
                }
                $member->delete();
                session()->flash('status', 'تم حذف العضو بنجاح.');
            }
        }
    }

    public function deleteFamily()
    {
        $photos = $this->family->members()->whereNotNull('photo_path')->pluck('photo_path')->all();

        DB::transaction(function () {
            // Delete the family's leader accounts so they can't keep logging in without a family.
            // Their shared lesson materials are kept (lesson_resources.user_id becomes null).
            User::where('family_id', $this->family->id)->where('role', 'leader')->delete();

            // Members, meetings, tatmim records and reports are removed by the database cascade.
            $this->family->delete();
        });

        if ($photos) {
            Storage::disk('public')->delete($photos);
        }

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
