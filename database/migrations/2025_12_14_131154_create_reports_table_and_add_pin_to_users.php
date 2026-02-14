<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إضافة كود الحماية للمستخدم
        Schema::table('users', function (Blueprint $table) {
            $table->string('report_pin')->nullable(); // كود من 4-6 أرقام
        });

        // 2. إنشاء جدول التقارير
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['weekly', 'monthly']);
            $table->date('report_date'); // تاريخ التقرير (الجمعة للأسبوعي، أول الشهر للشهري)

            // بيانات التقرير الأسبوعي
            $table->json('timeline')->nullable(); // جدول المواعيد (JSON)
            $table->text('weekly_achievements')->nullable(); // ما تم تنفيذه + أفكار

            // بيانات التقرير الشهري
            $table->text('monthly_summary')->nullable(); // ملخص الشهر
            $table->text('members_notes')->nullable(); // ملاحظات عن المخدومين
            $table->json('stats_snapshot')->nullable(); // لقطة من الإحصائيات وقت التقرير

            // مشترك
            $table->text('priest_message')->nullable(); // رسالة للأب الكاهن
            $table->text('admin_reply')->nullable(); // رد الأدمن
            $table->timestamp('admin_reply_at')->nullable(); // وقت الرد

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('report_pin');
        });
    }
};
