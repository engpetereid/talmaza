<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Livewire\WithFileUploads; // <-- استدعاء ميزة رفع الملفات

#[Layout('layouts.app')]
class MyFamily extends Component
{
    use WithFileUploads; // <-- تفعيل ميزة رفع الملفات

    // Variables
    public $new_member_name = '';
    public $new_member_phone = '';
    public $new_address = '';
    public $new_birth_date = '';
    public $new_job_or_college = '';
    public $new_spouse_name = '';
    public $new_children_details = '';
    public $new_confession_father = '';
    public $new_church_name = '';
    public $new_service_name = '';
    public $new_talents = '';
    public $photo; // <-- متغير الصورة

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
            'new_address' => 'nullable|string|max:255',
            'new_birth_date' => 'nullable|date',
            'new_job_or_college' => 'nullable|string|max:255',
            'new_spouse_name' => 'nullable|string|max:255',
            'new_children_details' => 'nullable|string|max:1000',
            'new_confession_father' => 'nullable|string|max:255',
            'new_church_name' => 'nullable|string|max:255',
            'new_service_name' => 'nullable|string|max:255',
            'new_talents' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048', // <-- تحقق من الصورة (أقصى حجم 2 ميجا)
        ], [
            'new_member_name.required' => 'اكتب اسم المخدوم.',
            'new_member_name.min' => 'الاسم قصير جداً.',
            'photo.image' => 'يجب أن يكون الملف صورة.',
            'photo.max' => 'حجم الصورة يجب ألا يتعدى 2 ميجابايت.',
        ]);

        // حفظ الصورة لو موجودة
        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('member_photos', 'public');
        }

        $family->members()->create([
            'name' => $this->new_member_name,
            'phone' => $this->new_member_phone,
            'address' => $this->new_address,
            'birth_date' => $this->new_birth_date ?: null,
            'job_or_college' => $this->new_job_or_college,
            'spouse_name' => $this->new_spouse_name,
            'children_details' => $this->new_children_details,
            'confession_father' => $this->new_confession_father,
            'church_name' => $this->new_church_name,
            'service_name' => $this->new_service_name,
            'talents' => $this->new_talents,
            'photo_path' => $photoPath, // <-- مسار الصورة
            'is_active' => true,
        ]);

        $this->reset([
            'new_member_name', 'new_member_phone', 'new_address', 'new_birth_date',
            'new_job_or_college', 'new_spouse_name', 'new_children_details',
            'new_confession_father', 'new_church_name', 'new_service_name',
            'new_talents', 'photo', 'showAddForm' // <-- تصفير الصورة
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
