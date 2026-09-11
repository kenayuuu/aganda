<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgandaPair extends Model
{
    use HasFactory;

    protected $table = 'aganda_pairs';

    protected $fillable = [
        'group_id',
        'left_member_id',
        'right_member_id',
        'bonus_amount',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(AgandaGroup::class, 'group_id');
    }

    public function leftMember()
    {
        return $this->belongsTo(User::class, 'left_member_id');
    }

    public function rightMember()
    {
        return $this->belongsTo(User::class, 'right_member_id');
    }
}
