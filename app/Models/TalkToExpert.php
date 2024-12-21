<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalkToExpert extends Model
{
    use HasFactory;

    protected $table = 'talk_expert_call';

    protected $fillable = [
        'user_id',
        'query_type',
        'plan',
        'call_datetime',
        'language',
        'message',
        'status',
        'coupon_id',
        'total_amount',
        'documents',
        'created_at', // These columns are automatically added by Laravel, but you can explicitly include them if you want
        'updated_at', // Same for updated_at
    ];
}
