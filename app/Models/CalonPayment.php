<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonPayment extends Model
{
    use HasFactory;

    protected $table = 'calon_payments';

    protected $fillable = [
        'calon_id',
        'package_kegiatan_id',
        'payment_type',
        'package_price',
        'deposit_amount',
        'amount',
        'status',
        'paid_at',
        'confirmed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function calon()
    {
        return $this->belongsTo(
            Calon::class,
            'calon_id'
        );
    }

    public function packageKegiatan()
    {
        return $this->belongsTo(
            PackageKegiatan::class,
            'package_kegiatan_id'
        );
    }

    public function confirmedBy()
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }

    public function member()
    {
        return $this->hasOne(
            User::class,
            'calon_id',
            'calon_id'
        );
    }
}
