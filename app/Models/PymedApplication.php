<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PymedApplication extends Model
{
    protected $fillable = [
        'user_id', 'pymed_job_id', 'resume_path', 'cover_letter',
        'status', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(PymedJob::class, 'pymed_job_id');
    }
}
