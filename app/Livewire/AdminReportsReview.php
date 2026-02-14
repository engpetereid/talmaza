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

    public function render()
    {
        $query = Report::with('family')->latest('report_date');

        // تطبيق الفلاتر
        if ($this->filter == 'pending') {

            $query->whereNull('admin_reply_at');
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
