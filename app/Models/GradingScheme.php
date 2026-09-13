<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_marks',
        'is_active',
    ];

    protected $casts = [
        'base_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function gradingRules()
    {
        return $this->hasMany(GradingRule::class);
    }
}