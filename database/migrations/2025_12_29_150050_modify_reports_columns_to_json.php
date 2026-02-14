<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // تحويل الأعمدة إلى JSON لتخزين المحتوى + الرد
            // سنقوم بإسقاط القديم وإنشاء جديد لضمان توافق البيانات (أو يمكنك استخدام change() لو مدعوم)
            // للتسهيل سنقوم بتعديل النوع، تأكد من أن الجدول فارغ أو قم بعمل fresh migrate إذا واجهت مشاكل

            $table->json('weekly_achievements')->nullable()->change();
            $table->json('monthly_summary')->nullable()->change();
            $table->json('priest_message')->nullable()->change();
            // members_notes موجودة بالفعل كـ JSON/Array في التعديل السابق
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('weekly_achievements')->nullable()->change();
            $table->text('monthly_summary')->nullable()->change();
            $table->text('priest_message')->nullable()->change();
        });
    }
};
