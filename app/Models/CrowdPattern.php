<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrowdPattern extends Model
{
    protected $fillable = [
        'day_of_week',
        'hour',
        'level',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'hour' => 'integer',
        'level' => 'integer',
    ];
}
