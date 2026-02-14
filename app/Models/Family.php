<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = ['name'];


    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function members()
    {
        return $this->hasMany(Member::class);
    }

  
    public function weeklyMeetings()
    {
        return $this->hasMany(WeeklyMeeting::class);
    }
}
