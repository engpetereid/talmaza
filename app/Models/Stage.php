<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order_number'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_number');
    }
}
