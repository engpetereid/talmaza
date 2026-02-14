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
        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // مين اللي رفع الملف
            $table->string('file_path')->nullable(); // مسار الملف لو فيه ملف
            $table->text('description')->nullable(); // وصف أو تلخيص نصي
            $table->string('type')->default('file'); // file or text
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_resources');
    }
};
