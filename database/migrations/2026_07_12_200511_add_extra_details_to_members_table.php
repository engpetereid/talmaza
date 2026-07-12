<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('address')->nullable()->after('phone');
            $table->string('spouse_name')->nullable()->after('job_or_college');
            $table->text('children_details')->nullable()->after('spouse_name');
            $table->string('church_name')->nullable()->after('confession_father');
            $table->string('service_name')->nullable()->after('church_name');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'spouse_name',
                'children_details',
                'church_name',
                'service_name'
            ]);
        });
    }
};
