<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PymedHeroSection extends Model
{
    protected $fillable = [
        'slug', 'image', 'bg_image', 'title', 'description', 'url',
    ];
}
