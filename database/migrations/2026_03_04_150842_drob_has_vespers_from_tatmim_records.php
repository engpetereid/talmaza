<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tatmim_records', function (Blueprint $table) {
            //
            $table->dropColumn('has_vespers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tatmim_records', function (Blueprint $table) {
            //
            $table->boolean('has_vespers')->after('has_weekly_kholwa');
        });
    }
};
