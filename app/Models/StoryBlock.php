<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryBlock extends Model
{
    protected $fillable = [
        'page',
        'key',
        'title',
        'body',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];
}
