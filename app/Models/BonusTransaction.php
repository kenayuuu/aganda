<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;

    protected $table = 'bonus_transactions';

    protected $fillable = [
        'user_id',
        'group_id',
        'source_user_id',
        'source_group_id',
        'type',
        'amount',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function group()
    {
        return $this->belongsTo(
            AgandaGroup::class,
            'group_id'
        );
    }

    public function sourceUser()
    {
        return $this->belongsTo(
            User::class,
            'source_user_id'
        );
    }

    public function allocations()
    {
        return $this->hasMany(
            BonusAllocation::class,
            'bonus_transaction_id'
        );
    }

    public function sourcePayment()
{
    return $this->belongsTo(
        CalonPayment::class,
        'source_payment_id'
    );
}
}
