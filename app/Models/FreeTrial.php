<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeTrial extends Model
{
    protected $table = 'freetrial';

    protected $fillable = [
        "name",
        "prise",
        "description",
    ];

}
