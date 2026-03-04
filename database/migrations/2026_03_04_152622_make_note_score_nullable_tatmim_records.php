<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tatmim_records', function (Blueprint $table) {

            $table->integer('note_score')->nullable()->change();
            $table->integer('kholwa_count')->nullable()->change();
            $table->integer('talmaza_training_count')->nullable()->change();

        });
    }

    public function down(): void
    {
        Schema::table('tatmim_records', function (Blueprint $table) {

            $table->integer('note_score')->nullable(false)->change();
            $table->integer('kholwa_count')->nullable(false)->change();
            $table->integer('talmaza_training_count')->nullable(false)->change();

        });
    }
};
