<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdsQuerie extends Model
{
    use HasFactory;

    protected $table = 'tds_tcs_queries';

    protected $fillable = [
        'tan_number',
        'no_of_employees',
        'no_of_entries',
        'tax_planning_of_employees',
        'type_of_return',
        'user_id',
        'status',
        'coupon_id',
        'default_discount',
        'total_amount',
    ];
}

