<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'master_kategoris';
    protected $fillable = ['name'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tahapans()
    {
        return $this->hasMany(Tahapan::class);
    }
}
