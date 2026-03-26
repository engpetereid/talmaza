<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('type')->default('announcement')->after('user_id'); // للتمييز: announcement أو decision
            $table->string('status')->default('pending')->after('content'); // الحالات: pending, implemented, not_implemented, postponed
            $table->text('admin_comment')->nullable()->after('status'); // تعقيب الإدارة
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['type', 'status', 'admin_comment']);
        });
    }
};
