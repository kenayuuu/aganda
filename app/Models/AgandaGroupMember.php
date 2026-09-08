<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgandaGroupMember extends Model
{
    use HasFactory;

    protected $table = 'aganda_group_members';

    protected $fillable = [
        'group_id',
        'calon_id',
        'registered_by',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(
            AgandaGroup::class,
            'group_id'
        );
    }

    public function calon()
    {
        return $this->belongsTo(
            Calon::class,
            'calon_id'
        );
    }

    public function registeredBy()
    {
        return $this->belongsTo(
            User::class,
            'registered_by'
        );
    }
}
