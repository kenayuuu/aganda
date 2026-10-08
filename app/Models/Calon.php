<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calon extends Model
{
    protected $table = 'calons';

    protected $fillable = [
        'nama_lengkap',
        'umur',
        'alamat',
        'no_paspor',
        'no_kk',
        'no_ktp',
        'akta_kelahiran',
        'no_telepon',
        'email',
        'jenis_perjalanan',
        'tanggal_berangkat',
        'package_kegiatan_id',
        'nama_bank',
        'no_rekening',
        'catatan',
    ];

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

    public function user()
    {
        return $this->hasOne(
            User::class,
            'calon_id',
            'id'
        );
    }
}
