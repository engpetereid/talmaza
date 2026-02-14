<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
class UserProfile extends Component
{
    // Profile Data
    public $name = '';
    public $phone = '';

    // Password Change Data
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    public $report_pin = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = $user->phone;
    }

    // Update Report PIN
    public function updateReportPin()
    {
        $this->validate([
            'report_pin' => 'required|digits_between:4,6',
        ], [
            'report_pin.required' => 'من فضلك أدخل الكود.',
            'report_pin.digits_between' => 'الكود يجب أن يكون أرقام فقط (من 4 لـ 6 أرقام).',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['report_pin' => $this->report_pin]);

        $this->reset('report_pin');
        session()->flash('pin-status', 'تم تعيين كود حماية التقارير بنجاح 🔒');
    }

    /**
     * Update Basic Info
     */
    public function updateProfileInformation()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('phone')) {
            $user->phone_verified_at = null;
        }

        $user->save();

        session()->flash('profile-status', 'تم تحديث البيانات بنجاح ✅');
    }

    /**
     * Update Password
     */
    public function updatePassword()
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('password-status', 'تم تغيير كلمة المرور بنجاح 🔒');
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
    public function aboutMe()
    {
        return redirect('/about-me');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}
