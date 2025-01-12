<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItrQuerie extends Model
{
    use HasFactory;
    protected $table = 'itr_queries';

    protected $fillable = [
        'user_id',
        'income_type',
        'resident',
        'business_income',
        'profit_loss',
        'income_tax_forms',
        'services',
        'coupon_id',
        'amount',
    ];
}
