<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'week_date',
        'status',
        'lesson_id',
        'custom_topic',
        'training_text',
        'max_note_score'
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function tatmimRecords()
    {
        return $this->hasMany(TatmimRecord::class);
    }
}
