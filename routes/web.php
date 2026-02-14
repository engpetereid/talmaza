<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdmin;
use App\Livewire\AboutMe;
use App\Livewire\UserProfile;
use App\Livewire\NotificationsList;
use App\Livewire\LessonLibrary;
use App\Livewire\AnnouncementsBoard;
use App\Livewire\ReportForm;
use App\Livewire\LeaderReports;
use App\Livewire\MyFamily;
use App\Livewire\RecordTatmim;
use App\Livewire\LeaderStats;
use App\Livewire\StageStats;
use App\Livewire\MemberStats;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminFamilyView;
use App\Livewire\AdminFamilyStats;
use App\Livewire\AdminFamilyStageStats;
use App\Livewire\AdminReportsReview;
use App\Livewire\AddFamily;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/profile', UserProfile::class)->name('profile');
    Route::get('/notifications', NotificationsList::class)->name('notifications');
    Route::get('/announcements', AnnouncementsBoard::class)->name('announcements');
    Route::get('/lessons', LessonLibrary::class)->name('lessons.library');


    Route::get('/reports/create/{type}', ReportForm::class)->name('report.form');

    Route::get('/reports/view/{report}', ReportForm::class)->name('report.view');


    Route::get('/reports', LeaderReports::class)->name('leader.reports'); 
    Route::get('/my-family', MyFamily::class)->name('my-family');
    Route::get('/meeting/{meeting}/record', RecordTatmim::class)->name('meeting.record');
    Route::get('/stats', LeaderStats::class)->name('stats');
    Route::get('/stage-stats', StageStats::class)->name('stage.stats');
    Route::get('/members/{member}/stats', MemberStats::class)->name('member.stats');


    Route::middleware(EnsureAdmin::class)->prefix('admin')->name('admin.')->group(function () {


        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        Route::get('/families/create', AddFamily::class)->name('add-family');
        Route::get('/families/{family}', AdminFamilyView::class)->name('family.view');
        Route::get('/families/{family}/stats', AdminFamilyStats::class)->name('family.stats');
        Route::get('/families/{family}/stage-stats', AdminFamilyStageStats::class)->name('family.stage_stats');
        Route::get('/reports-review', AdminReportsReview::class)->name('reports');
    });
    Route::get('/about-me', AboutMe::class)->name('about-me');

});

require __DIR__.'/auth.php';
