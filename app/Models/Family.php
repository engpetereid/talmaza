<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{

    protected $fillable = ['name'];


    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * The leader account of this family.
     */
    public function leader()
    {
        return $this->hasOne(User::class)->where('role', 'leader');
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
