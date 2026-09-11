<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusWithdrawal extends Model
{
    use HasFactory;

    protected $table = 'bonus_withdrawals';

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'requested_at',
        'processed_at',
        'processed_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function processedBy()
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }
}
