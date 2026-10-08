<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgandaRewardAward extends Model
{
    use HasFactory;

    protected $table = 'aganda_reward_awards';

    protected $fillable = [
        'member_id',
        'group_id',
        'group_code',
        'reward_level',
        'reward_name',
        'target_value',
        'target_unit',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function group()
    {
        return $this->belongsTo(AgandaGroup::class, 'group_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
