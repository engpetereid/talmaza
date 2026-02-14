<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tatmim_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_meeting_id')->constrained('weekly_meetings')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');

            // بنود التتميم
            $table->boolean('is_present')->default(false); // حضور الجلسة
            $table->integer('note_score')->default(0); // درجة النوتة اللي حققها

            // البنود الثابتة (صح أو غلط)
            $table->boolean('has_mass')->default(false); // قداس
            $table->boolean('has_servants_meeting')->default(false); // اجتماع خدام
            $table->boolean('has_vespers')->default(false); // عشية
            $table->boolean('has_tasbeha')->default(false); // تسبحة
            $table->boolean('has_reading')->default(false); // قراءة كتاب
            $table->boolean('has_family_altar')->default(false); // مذبح عائلي

            $table->integer('kholwa_count')->default(0); // عدد مرات الخلوة (من 7)
            $table->text('comments')->nullable(); // ملاحظات

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tatmim_records');
    }
};
