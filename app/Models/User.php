<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'name',
        'email',
        'avatar',
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

    public function calon()
    {
        return $this->belongsTo(
            Calon::class,
            'calon_id'
        );
    }

    public function calonPayments()
    {
        return $this->hasMany(
            CalonPayment::class,
            'confirmed_by'
        );
    }

    public function bonusTransactions()
    {
        return $this->hasMany(
            BonusTransaction::class,
            'user_id'
        );
    }

    public function bonusAllocations()
    {
        return $this->hasMany(
            BonusAllocation::class,
            'user_id'
        );
    }

    public function bonusWithdrawals()
    {
        return $this->hasMany(
            BonusWithdrawal::class,
            'user_id'
        );
    }

    public function processedWithdrawals()
    {
        return $this->hasMany(
            BonusWithdrawal::class,
            'processed_by'
        );
    }
}
