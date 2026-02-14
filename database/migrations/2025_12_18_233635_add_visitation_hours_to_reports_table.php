<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // إضافة عمود لساعات الافتقاد (يقبل كسور عشرية، مثلاً 1.5 ساعة)
            $table->decimal('visitation_hours', 5, 2)->nullable()->after('weekly_achievements');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('visitation_hours');
        });
    }
};
