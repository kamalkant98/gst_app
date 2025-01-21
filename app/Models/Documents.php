<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;
    protected $table = 'documents';

    protected $fillable = [
        'query_id',
        'file_url',
        'form_type',
        'created_at', // These columns are automatically added by Laravel, but you can explicitly include them if you want
        'updated_at', // Same for updated_at
    ];
}
