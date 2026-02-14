<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// يشتغل كل يوم جمعة الساعة 1 صباحاً
Schedule::command('app:open-new-week')->weeklyOn(5, '01:00');

// --- 2. فحص الغياب المتكرر ---
// يشتغل كل يوم أحد الساعة 8 صباحاً (بعد اجتماعات الجمعة)
Schedule::command('app:check-absence')->weeklyOn(0, '08:00');
