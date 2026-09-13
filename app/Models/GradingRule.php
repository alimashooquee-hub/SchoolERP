<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_scheme_id',
        'grade',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'remarks',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function gradingScheme()
    {
        return $this->belongsTo(GradingScheme::class);
    }
}