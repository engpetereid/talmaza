<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Report;
use App\Models\Family;
use Carbon\Carbon;

#[Layout('layouts.app')]
class AdminReportsReview extends Component
{
    use WithPagination;

    public $filter = 'all';
    public $filterMonth;
    public $filterYear;
    public $availableYears = [];

    public function mount()
    {
        // تعيين الفلاتر الافتراضية على الشهر والسنة الحالية
        $this->filterMonth = Carbon::now()->month;
        $this->filterYear = Carbon::now()->year;

        // جلب آخر 3 سنوات للفلتر
        $this->availableYears = range(Carbon::now()->year, Carbon::now()->year - 2);
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage(); // الرجوع للصفحة الأولى عند تغيير الفلتر
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    /**
     * Delete a specific report
     */
    public function deleteReport($id)
    {
        $report = Report::find($id);

        if ($report) {
            $report->delete();
            session()->flash('message', 'تم حذف التقرير بنجاح 🗑️');
        }
    }

    public function render()
    {
        // ترتيب التقارير حسب آخر نشاط (رد) لكي تصعد المحادثات النشطة للأعلى
        $query = Report::with('family')->latest('created_at');

        // 1. تطبيق فلاتر الشهر والسنة على التقارير
        if ($this->filterMonth !== 'all') {
            $query->whereMonth('report_date', $this->filterMonth);
        }
        if ($this->filterYear !== 'all') {
            $query->whereYear('report_date', $this->filterYear);
        }

        // 2. تطبيق فلاتر النوع والحالة
        if ($this->filter == 'pending') {
            $query->where(function ($q) {
                // يحتاج لرد إذا لم يتم الرد عليه أبداً، أو إذا قام القائد بالرد بعد آخر رد للإدارة
                $q->whereNull('admin_reply_at')
                    ->orWhereColumn('updated_at', '>', 'admin_reply_at');
            });
        } elseif ($this->filter == 'weekly') {
            $query->where('type', 'weekly');
        } elseif ($this->filter == 'monthly') {
            $query->where('type', 'monthly');
        }

        // 3. جلب العائلات المتأخرة عن التقرير الشهري (فقط إذا تم اختيار فلتر الشهري)
        $missingFamilies = collect();
        if ($this->filter === 'monthly') {
            // جلب أرقام العائلات التي أرسلت التقرير الشهري في التاريخ المحدد
            $submittedFamilyIds = Report::where('type', 'monthly')
                ->when($this->filterMonth !== 'all', fn($q) => $q->whereMonth('report_date', $this->filterMonth))
                ->when($this->filterYear !== 'all', fn($q) => $q->whereYear('report_date', $this->filterYear))
                ->pluck('family_id')
                ->toArray();

            // جلب العائلات التي غير موجودة في القائمة السابقة
            $missingFamilies = Family::with(['user' => function ($q) {
                $q->where('role', 'leader');
            }])->whereNotIn('id', $submittedFamilyIds)->get();
        }

        return view('livewire.admin-reports-review', [
            'reports' => $query->paginate(12),
            'missingFamilies' => $missingFamilies
        ]);
    }
}
