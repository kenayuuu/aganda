<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'parent_id',
        'calon_id',
        'member_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function agandaGroups()
    {
        return $this->hasMany(
            AgandaGroup::class,
            'owner_id'
        );
    }

    public function parent()
    {
        return $this->belongsTo(
            User::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            User::class,
            'parent_id'
        );
    }
}
