<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Report;

#[Layout('layouts.app')]
class AdminReportsReview extends Component
{
    use WithPagination;

    public $filter = 'all';

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage(); // الرجوع للصفحة الأولى عند تغيير الفلتر
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
        $query = Report::with('family')->latest('updated_at');

        // تطبيق الفلاتر
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

        return view('livewire.admin-reports-review', [
            'reports' => $query->paginate(12)
        ]);
    }
}
