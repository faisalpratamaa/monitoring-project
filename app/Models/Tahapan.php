<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahapan extends Model
{
    protected $fillable = [
        'name',
        'bobot',
        'project_id',
        'progress',
        'nilai',
        'file',
    ];

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }
}
