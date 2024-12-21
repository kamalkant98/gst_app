<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstQuerie extends Model
{
    use HasFactory;

    protected $table = 'gst_queries';

    protected $fillable = [
        'gst_number',
        'type_of_taxpayer',
        'return_filling_frequency',
        'type_of_return',
        'service_type',
        'user_id',
        'status',
        'coupon_id',
        'total_amount',
    ];
}

