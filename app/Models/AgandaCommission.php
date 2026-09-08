<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgandaCommission extends Model
{
    use HasFactory;

    protected $table = 'aganda_commissions';

    protected $fillable = [
        'group_id',
        'user_id',
        'calon_id',
        'amount',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(
            AgandaGroup::class,
            'group_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function calon()
    {
        return $this->belongsTo(
            Calon::class,
            'calon_id'
        );
    }
}
