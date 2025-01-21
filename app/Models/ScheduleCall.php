<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCall extends Model
{
    use HasFactory;
    protected $table = 'schedule_call';

    protected $fillable = [
        'user_id',
        'query_type',
        'plan',
        'call_datetime',
        'language',
        'message',
        'status',
        'coupon_id',
        'default_discount',
        'total_amount',
        'call_when',
        'created_at', // These columns are automatically added by Laravel, but you can explicitly include them if you want
        'updated_at', // Same for updated_at
    ];
}
