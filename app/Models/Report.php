<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'type',
        'report_date',
        'timeline',
        'weekly_achievements',
        'visitation_hours',
        'monthly_summary',
        'members_notes',
        'stats_snapshot',
        'priest_message',
        'admin_reply',
        'admin_reply_at'
    ];

    protected $casts = [
        'report_date' => 'date',
        'timeline' => 'array',
        'stats_snapshot' => 'array',
        'members_notes' => 'array',

        'weekly_achievements' => 'array',
        'monthly_summary' => 'array',
        'priest_message' => 'array',

        'admin_reply_at' => 'datetime',
        'visitation_hours' => 'decimal:2',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}
