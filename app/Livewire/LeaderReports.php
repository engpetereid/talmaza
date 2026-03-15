<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

#[Layout('layouts.app')]
class LeaderReports extends Component
{
    // Lock Screen Variables
    public $isLocked = true;
    public $pinAttempt = '';
    public $errorMsg = '';

    public function mount()
    {
        // 1. Check Session: Is it already unlocked?
        if (session()->has('reports_unlocked_' . Auth::id())) {
            $this->isLocked = false;
        }

        // 2. Check Requirement: Does user have a PIN?
        if (!Auth::user()->report_pin) {
            return $this->redirectRoute('profile')->with('pin-status', 'يرجى تعيين كود حماية للتقارير أولاً 🔒');
        }
    }

    /**
     * Attempt to unlock
     */
    public function unlock()
    {
        if ($this->pinAttempt == Auth::user()->report_pin) {
            $this->isLocked = false;
            session()->put('reports_unlocked_' . Auth::id(), true);
            $this->errorMsg = '';
        } else {
            $this->errorMsg = 'الكود غير صحيح ❌';
            $this->pinAttempt = '';
        }
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
