<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calon extends Model
{
    protected $table = 'calons';

    public function packageKegiatan()
    {
        return $this->belongsTo(
            PackageKegiatan::class,
            'package_kegiatan_id'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            CalonPayment::class,
            'calon_id'
        );
    }
}
