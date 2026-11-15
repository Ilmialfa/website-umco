<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'avatar_path',
        'rating',
        'body',
        'source',
        'is_featured',
        'is_sample',
        'sort',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_sample' => 'boolean',
        'sort' => 'integer',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('sort');
    }
}
