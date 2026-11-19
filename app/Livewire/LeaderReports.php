<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Report;

#[Layout('layouts.app')]
class LeaderReports extends Component
{
    private const MAX_PIN_ATTEMPTS = 5;
    private const PIN_LOCKOUT_SECONDS = 300;

    // Lock Screen Variables
    // #[Locked] prevents the browser from flipping this value directly (PIN bypass).
    #[Locked]
    public $isLocked = true;
    public $pinAttempt = '';
    public $errorMsg = '';

    public function mount()
    {
        // 1. Check Requirement: Does user have a PIN?
        if (!Auth::user()->report_pin) {
            session()->flash('pin-status','يرجى تعيين كود حماية للتقارير أولاً 🔒');
            $this->redirect(route('profile'),navigate: true);
            return;
        }

        // 2. Check Session: Is it already unlocked?
        if (session()->has($this->sessionKey())) {
            $this->isLocked = false;
        }
    }

    /**
     * Attempt to unlock
     */
    public function unlock()
    {
        $throttleKey = 'report-pin:' . Auth::id();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_PIN_ATTEMPTS)) {
            $minutes = ceil(RateLimiter::availableIn($throttleKey) / 60);
            $this->errorMsg = "محاولات كثيرة خاطئة، حاول مرة أخرى بعد {$minutes} دقيقة ⏳";
            $this->pinAttempt = '';
            return;
        }

        if (is_string($this->pinAttempt) && $this->pinAttempt !== '' && $this->pinMatches($this->pinAttempt)) {
            RateLimiter::clear($throttleKey);
            $this->isLocked = false;
            session()->put($this->sessionKey(), true);
            $this->errorMsg = '';
        } else {
            RateLimiter::hit($throttleKey, self::PIN_LOCKOUT_SECONDS);
            $this->errorMsg = 'الكود غير صحيح ❌';
        }

        $this->pinAttempt = '';
    }

    /**
     * Delete a specific report
     */
    public function deleteReport($id)
    {
        if ($this->isLocked) return;

        // Ensure the report belongs to the current user's family
        $report = Report::where('id', $id)
            ->where('family_id', Auth::user()->family_id)
            ->first();

        if ($report) {
            $report->delete();
            session()->flash('message', 'تم حذف التقرير بنجاح 🗑️');
        }
    }

    /**
     * Check the PIN. Handles legacy plain-text PINs (from before PINs were hashed):
     * they are compared safely and upgraded to a hash on the first successful unlock.
     */
    private function pinMatches(string $attempt): bool
    {
        $user = Auth::user();
        $storedPin = $user->getRawOriginal('report_pin');

        if (!$storedPin) {
            return false;
        }

        if (Hash::isHashed($storedPin)) {
            return Hash::check($attempt, $storedPin);
        }

        if (!hash_equals((string) $storedPin, $attempt)) {
            return false;
        }

        // Upgrade the legacy plain-text PIN (the 'hashed' cast hashes it on save)
        $user->forceFill(['report_pin' => $attempt])->save();

        return true;
    }

    private function sessionKey(): string
    {
        return 'reports_unlocked_' . Auth::id();
    }

    public function render()
    {
        $reports = [];

        // Fetch data only if unlocked
        if (!$this->isLocked) {
            // التعديل: الترتيب حسب آخر نشاط لكي تظهر المحادثات المتجددة في الأعلى
            $reports = Report::where('family_id', Auth::user()->family_id)
                ->latest('updated_at')
                ->get();
        }

        return view('livewire.leader-reports', [
            'reports' => $reports
        ]);
    }
}
