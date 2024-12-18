<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRegistration extends Model
{
    use HasFactory;

    protected $table = 'business_registrations';

    protected $fillable = [
        'registration_type',
        'documents',
        'user_inquiry_id'
    ];
}
