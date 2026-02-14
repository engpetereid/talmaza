<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TatmimRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_meeting_id',
        'member_id',
        'is_present',
        'note_score',
        'has_mass',
        'has_servants_meeting',
        'has_vespers',
        'has_tasbeha',
        'has_reading',
        'has_family_altar',
        'kholwa_count',
        'comments',
        'talmaza_training_count',
        'has_weekly_kholwa'
    ];

    protected $casts = [
        'is_present' => 'boolean',
        'has_mass' => 'boolean',
        'has_servants_meeting' => 'boolean',
        'has_vespers' => 'boolean',
        'has_tasbeha' => 'boolean',
        'has_reading' => 'boolean',
        'has_family_altar' => 'boolean',
        'has_weekly_kholwa' => 'boolean',
    ];

    public function weeklyMeeting()
    {
        return $this->belongsTo(WeeklyMeeting::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
