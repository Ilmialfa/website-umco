<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreetStall extends Model
{
    protected $fillable = [
        'name',
        'signature_item',
        'description',
        'active_hours',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];
}
