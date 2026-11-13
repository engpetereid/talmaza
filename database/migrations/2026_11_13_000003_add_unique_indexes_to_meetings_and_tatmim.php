<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Weekly meetings: one meeting per family per date.
        //    Duplicates are NOT deleted automatically (deleting a meeting cascades to its tatmim records).
        $meetingDupes = DB::table('weekly_meetings')
            ->select('family_id', 'week_date', DB::raw('COUNT(*) as total'))
            ->groupBy('family_id', 'week_date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($meetingDupes->isNotEmpty()) {
            $list = $meetingDupes->map(fn ($d) => "family {$d->family_id} on {$d->week_date} ({$d->total}x)")->implode(', ');
            throw new RuntimeException(
                "Duplicate weekly meetings found: {$list}. Remove the extra (usually empty/pending) meetings manually, then run the migration again."
            );
        }

        // 2. Tatmim records: duplicates are identical copies (updates always hit every matching row),
        //    so keep the oldest row and delete the rest.
        $recordDupes = DB::table('tatmim_records')
            ->select('weekly_meeting_id', 'member_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('weekly_meeting_id', 'member_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($recordDupes as $dupe) {
            DB::table('tatmim_records')
                ->where('weekly_meeting_id', $dupe->weekly_meeting_id)
                ->where('member_id', $dupe->member_id)
                ->where('id', '!=', $dupe->keep_id)
                ->delete();
        }

        Schema::table('weekly_meetings', function (Blueprint $table) {
            $table->unique(['family_id', 'week_date']);
        });

        Schema::table('tatmim_records', function (Blueprint $table) {
            $table->unique(['weekly_meeting_id', 'member_id']);
        });
    }

    public function down(): void
    {
        // MySQL needs a plain index on the foreign-key column before the composite unique can be dropped.
        Schema::table('tatmim_records', function (Blueprint $table) {
            $table->index('weekly_meeting_id');
            $table->dropUnique(['weekly_meeting_id', 'member_id']);
        });

        Schema::table('weekly_meetings', function (Blueprint $table) {
            $table->index('family_id');
            $table->dropUnique(['family_id', 'week_date']);
        });
    }
};
