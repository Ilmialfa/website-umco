<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClosedDate extends Model
{
    protected $fillable = [
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];
}
