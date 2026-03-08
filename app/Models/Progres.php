<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progres extends Model
{
    protected $table = 'progres';
    protected $fillable = ['detail_project_id', 'tanggal', 'user_id', 'file', 'nilai'];

    public function detailProject()
    {
        return $this->belongsTo(DetailProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
