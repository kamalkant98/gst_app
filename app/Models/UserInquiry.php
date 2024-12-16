<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInquiry extends Model
{
    use HasFactory;

    protected $table = 'users_inquiry';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'form_type',
        'message',
        'otp',
        'otp_expires_at', // If you're using an expiration time for the OTP
        'is_verified',
        'created_at', // These columns are automatically added by Laravel, but you can explicitly include them if you want
        'updated_at', // Same for updated_at
    ];
}
