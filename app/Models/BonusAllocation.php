<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusAllocation extends Model
{
    use HasFactory;

    protected $table = 'bonus_allocations';

    protected $fillable = [
        'user_id',
        'bonus_transaction_id',
        'allocation_type',
        'amount',
        'reference_id',
        'notes',
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

    public function bonusTransaction()
    {
        return $this->belongsTo(
            BonusTransaction::class,
            'bonus_transaction_id'
        );
    }
}
