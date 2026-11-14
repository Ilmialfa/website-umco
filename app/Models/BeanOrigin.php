<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeanOrigin extends Model
{
    protected $fillable = [
        'name',
        'region',
        'altitude',
        'note',
        'sort',
    ];

    protected $casts = [
        'altitude' => 'integer',
        'sort' => 'integer',
    ];
}
