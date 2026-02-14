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
        Schema::create('weekly_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
            $table->date('week_date'); // تاريخ الجمعة بداية الأسبوع

            // تفاصيل الاجتماع
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending'); // حالة الاجتماع
            $table->foreignId('lesson_id')->nullable()->constrained('lessons'); // لو أخدوا درس من المنهج
            $table->string('custom_topic')->nullable(); // لو موضوع خارجي
            $table->text('training_text')->nullable(); // نص التدريب
            $table->integer('max_note_score')->default(100); // القائد بيحدد النوتة الأسبوع ده من كام

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_meetings');
    }
};
