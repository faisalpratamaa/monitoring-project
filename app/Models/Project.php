<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'master_projects';
    protected $fillable = ['name', 'kode', 'kategori_id', 'bobot', 'target', 'anggaran', 'waktu', 'tipe', 'pic', 'no_hp', 'email'];

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
