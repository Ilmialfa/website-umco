<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryBlock extends Model
{
    use HasFactory;

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
