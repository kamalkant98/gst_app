<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'order_id',
        'form_type',
        'payment_method',
        'transaction_type',
        'amount',
        'currency',
        'coupon_code',
        'default_discount',
        'status',
        'description',
        'transaction_reference',
        'txnid',
        'invoice_id',
        'invoice',
        'hash'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2', // Ensures amounts are always cast as decimals with 2 precision
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
