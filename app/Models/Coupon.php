<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'type',
        'value',
        'expires_at',
        'created_at', // These columns are automatically added by Laravel, but you can explicitly include them if you want
        'updated_at', // Same for updated_at
    ];
}
