<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'stage_id', 'order_number'];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    public function resources()
    {
        return $this->hasMany(LessonResource::class)->latest();
    }
}
