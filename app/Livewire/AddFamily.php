<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Family;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class AddFamily extends Component
{
    // بيانات العائلة
    public $family_name = '';

    // بيانات القائد
    public $leader_name = '';
    public $leader_phone = '';
    public $leader_password = '';

    public function save()
    {
        //التحقق من البيانات
        $this->validate([
            'family_name' => 'required|string|max:255',
            'leader_name' => 'required|string|max:255',
            'leader_phone' => ['required', 'string', Rule::unique('users', 'phone')], // الرقم لازم يكون غير مكرر
            'leader_password' => 'required|string|min:6',
        ], [
            'leader_phone.unique' => 'رقم التليفون هذا مسجل من قبل لقائد آخر.',
        ]);

        //  إنشاء العائلة
        $family = Family::create([
            'name' => $this->family_name,
        ]);

        //  إنشاء حساب القائد وربطه بالعائلة
        User::create([
            'name' => $this->leader_name,
            'phone' => $this->leader_phone,
            'password' => Hash::make($this->leader_password),
            'role' => 'leader',
            'family_id' => $family->id,
        ]);

        //  رسالة نجاح والعودة للوحة التحكم
        return redirect()->route('admin.dashboard')->with('status', 'تم إضافة العائلة والقائد بنجاح! 🎉');
    }

    public function render()
    {
        return view('livewire.add-family');
    }
}
