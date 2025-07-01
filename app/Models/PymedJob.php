<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PymedJob extends Model
{
    protected $fillable = [
        'slug', 'title', 'image', 'sort_description', 'description',
        'quote', 'datas', 'status', 'published_at',
    ];

    protected $casts = [
        'datas' => 'array',
        'published_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(PymedApplication::class);
    }
}
