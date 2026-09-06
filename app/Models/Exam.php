<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_name',
        'exam_date',
        'class_room_id',
        'status',
        'description',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
}