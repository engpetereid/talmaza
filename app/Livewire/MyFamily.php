<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

#[Layout('layouts.app')]
class MyFamily extends Component
{
    // Variables
    public $new_member_name = '';
    public $new_member_phone = '';
    public $new_birth_date = '';
    public $new_job_or_college = '';
    public $new_confession_father = '';
    public $new_talents = '';

    // UI State
    public $showAddForm = false;

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
    }

    public function addMember()
    {
        $family = Auth::user()->family;
        if (!$family) return;

        $this->validate([
            'new_member_name' => 'required|string|min:3',
            'new_member_phone' => 'nullable|string|max:15',
            'new_birth_date' => 'nullable|date',
            'new_job_or_college' => 'nullable|string|max:255',
            'new_confession_father' => 'nullable|string|max:255',
            'new_talents' => 'nullable|string|max:500',
        ], [
            'new_member_name.required' => 'اكتب اسم المخدوم.',
            'new_member_name.min' => 'الاسم قصير جداً.',
        ]);

        $family->members()->create([
            'name' => $this->new_member_name,
            'phone' => $this->new_member_phone,
            'birth_date' => $this->new_birth_date ?: null,
            'job_or_college' => $this->new_job_or_college,
            'confession_father' => $this->new_confession_father,
            'talents' => $this->new_talents,
            'is_active' => true,
        ]);

        $this->reset([
            'new_member_name', 'new_member_phone', 'new_birth_date',
            'new_job_or_college', 'new_confession_father', 'new_talents',
            'showAddForm' 
        ]);

        session()->flash('message', 'تم إضافة المخدوم بنجاح! 🎉');
    }

    public function toggleActive($memberId)
    {
        $member = Member::find($memberId);
        if ($member && $member->family_id == Auth::user()->family_id) {
            $member->update(['is_active' => !$member->is_active]);
        }
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.my-family', [
            'family' => $user->family,
            'members' => $user->family
                ? $user->family->members()->orderBy('is_active', 'desc')->get()
                : []
        ]);
    }
}
