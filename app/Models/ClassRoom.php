<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_name',
        'section',
        'class_code',
        'capacity',
        'description',
        'status',
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_room_id');
    }
}