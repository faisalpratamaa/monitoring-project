<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProject extends Model
{
    protected $table = 'detail_projects';
    protected $fillable = ['tahapan_id', 'project_id', 'bobot', 'progres', 'nilai'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tahapan()
    {
        return $this->belongsTo(Tahapan::class);
    }

    public function progres()
    {
        return $this->hasMany(Progres::class);
    }
}
