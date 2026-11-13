<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Family;
use App\Models\WeeklyMeeting;
use Carbon\Carbon;

class OpenNewWeek extends Command
{
    /**
     * اسم الأمر في التيرمينال
     */
    protected $signature = 'app:open-new-week';

    /**
     * وصف الأمر
     */
    protected $description = 'فتح اجتماع أسبوعي جديد لكل العائلات تلقائياً';

    /**
     * تنفيذ الأمر
     */
    public function handle()
    {
        $this->info('جاري بدء فتح الأسبوع الجديد...');

        $today = Carbon::today();

        $count = 0;


        Family::chunk(100, function ($families) use ($today, &$count) {
            foreach ($families as $family) {
                // اجتماع واحد فقط لكل عائلة في نفس التاريخ (مضمون أيضاً بـ unique index)
                $meeting = WeeklyMeeting::firstOrCreate(
                    ['family_id' => $family->id, 'week_date' => $today->toDateString()],
                    ['status' => 'pending', 'max_note_score' => 28]
                );

                if ($meeting->wasRecentlyCreated) {
                    $count++;
                }
            }
        });

        $this->info("تم بنجاح! تم فتح اجتماع جديد لـ ($count) عائلة.");
    }
}
