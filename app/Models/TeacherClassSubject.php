<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherClassSubject extends Model
{
    use HasFactory;

    protected $table = 'teacher_class_subject';

    protected $fillable = [
        'teacher_id',
        'class_room_id',
        'subject_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}