<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgandaGroup extends Model
{
    use HasFactory;

    protected $table = 'aganda_groups';

    protected $fillable = [
        'kode_group',
        'owner_id',
        'package_kegiatan_id',
        'status',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function packageKegiatan()
    {
        return $this->belongsTo(
            PackageKegiatan::class,
            'package_kegiatan_id'
        );
    }

    public function members()
    {
        return $this->hasMany(
            AgandaGroupMember::class,
            'group_id'
        );
    }

    public function pairs()
    {
        return $this->hasMany(
            AgandaPair::class,
            'group_id'
        );
    }
}
