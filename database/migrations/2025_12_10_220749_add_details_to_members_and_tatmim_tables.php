<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. تعديل جدول المخدومين
        Schema::table('members', function (Blueprint $table) {
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->string('job_or_college')->nullable(); // الكلية أو الوظيفة
            $table->string('confession_father')->nullable(); // أب الاعتراف
            $table->text('talents')->nullable(); // المواهب
        });

        // 2. تعديل جدول سجلات التتميم
        Schema::table('tatmim_records', function (Blueprint $table) {
            $table->integer('talmaza_training_count')->default(0); // تدريب التلمذة (من 7)
            $table->boolean('has_weekly_kholwa')->default(false); // الخلوة الأسبوعية (أه أو لأ)
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'job_or_college', 'confession_father', 'talents']);
        });

        Schema::table('tatmim_records', function (Blueprint $table) {
            $table->dropColumn(['talmaza_training_count', 'has_weekly_kholwa']);
        });
    }
};
