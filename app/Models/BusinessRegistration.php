<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRegistration extends Model
{
    use HasFactory;

    protected $table = 'business_registrations';

    protected $fillable = [
        'plan',
        'documents',
        'user_id',
        'status',
        'coupon_id',
        'default_discount',
        'total_amount',
    ];
}
