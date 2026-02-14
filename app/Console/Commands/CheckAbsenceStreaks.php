<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Family;
use App\Notifications\MemberAbsentAlert;

class CheckAbsenceStreaks extends Command
{
    /**
     * اسم وتوقيع الأمر للتشغيل من التيرمينال
     * يمكن تشغيله عبر: php artisan app:check-absence
     */
    protected $signature = 'app:check-absence';

    /**
     * وصف الأمر
     */
    protected $description = 'فحص غياب المخدومين لآخر 3 اجتماعات وإرسال تنبيهات للقادة';

    /**
     * تنفيذ منطق الفحص
     */
    public function handle()
    {
        $this->info('جاري فحص سجلات الغياب...');

        // جلب العائلات مع القادة والمخدومين وآخر 3 اجتماعات مكتملة
        $families = Family::with(['users', 'members', 'weeklyMeetings' => function($q) {
            $q->where('status', 'completed')->latest('week_date')->take(3);
        }])->get();

        foreach ($families as $family) {
            // تخطي العائلات التي لم تعقد 3 اجتماعات على الأقل
            if ($family->weeklyMeetings->count() < 3) {
                continue;
            }

            // الحصول على قائد العائلة (أول مستخدم بدور leader)
            $leader = $family->users->where('role', 'leader')->first();

            // إذا لم يوجد قائد، لا يمكن إرسال إشعار
            if (!$leader) {
                continue;
            }

            foreach ($family->members as $member) {
                // تخطي الأعضاء غير النشطين
                if (!$member->is_active) {
                    continue;
                }

                $absentCount = 0;

                // فحص الحضور في آخر 3 اجتماعات
                foreach ($family->weeklyMeetings as $meeting) {
                    // البحث عن سجل التتميم الخاص بالعضو في هذا الاجتماع
                    $record = $meeting->tatmimRecords->where('member_id', $member->id)->first();

                    // إذا لم يوجد سجل أو مسجل كغائب
                    if (!$record || !$record->is_present) {
                        $absentCount++;
                    }
                }

                // إذا تغيب العضو 3 مرات متتالية
                if ($absentCount == 3) {
                    // التحقق من عدم تكرار الإشعار لنفس العضو في نفس اليوم
                    // ملاحظة: هذا يعتمد على نص الرسالة المخزن في JSON
                    $alreadyNotified = $leader->notifications()
                        ->where('type', MemberAbsentAlert::class)
                        ->where('data->message', 'like', "%{$member->name}%")
                        ->whereDate('created_at', today())
                        ->exists();

                    if (!$alreadyNotified) {
                        // إرسال الإشعار
                        $leader->notify(new MemberAbsentAlert($member));
                        $this->info("تم إرسال تنبيه للقائد بخصوص المخدوم: {$member->name}");
                    }
                }
            }
        }

        $this->info('تم الانتهاء من فحص الغياب.');
    }
}
