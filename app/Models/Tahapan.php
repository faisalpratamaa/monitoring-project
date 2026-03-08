<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahapan extends Model
{
    protected $table = 'tahapans';
    protected $fillable = ['kode', 'kategori_id', 'name'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailProjects()
    {
        return $this->hasMany(DetailProject::class);
    }

    public function subTahapans()
    {
        return $this->hasMany(SubTahapan::class);
    }
}
