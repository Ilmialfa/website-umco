<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = [
        'page',
        'key',
        'is_active',
        'sort',
        'heading',
        'subheading',
        'body',
        'cta_label',
        'cta_url',
        'media',
        'data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
        'media' => 'array',
        'data' => 'array',
    ];
}
