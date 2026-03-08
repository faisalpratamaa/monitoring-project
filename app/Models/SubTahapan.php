<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTahapan extends Model
{
    protected $table = 'sub_tahapans';
    protected $fillable = ['kode', 'tahapan_id', 'project_id', 'name', 'bobot', 'progres', 'nilai'];

    public function tahapan()
    {
        return $this->belongsTo(Tahapan::class);
    }

    public function progreses()
    {
        return $this->hasMany(Progres::class);
    }
}
