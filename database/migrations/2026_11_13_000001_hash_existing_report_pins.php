<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Report PINs used to be stored in plain text. Hash any that are not hashed yet.
     * (One-way: the original PINs cannot be restored, but users keep the same PIN.)
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('report_pin')
            ->where('report_pin', '!=', '')
            ->orderBy('id')
            ->each(function ($user) {
                if (!Hash::isHashed($user->report_pin)) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['report_pin' => Hash::make($user->report_pin)]);
                }
            });
    }

    public function down(): void
    {
        // Irreversible: hashed PINs cannot be converted back to plain text.
    }
};
