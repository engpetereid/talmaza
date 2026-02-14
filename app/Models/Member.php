<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'family_id',
        'is_active',
        'birth_date',
        'job_or_college',
        'confession_father',
        'talents'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function tatmimRecords()
    {
        return $this->hasMany(TatmimRecord::class);
    }
}
