<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prestashop_url',
        'prestashop_api_key',
    ];

    // Automatically encrypts and decrypts the API key
    protected $casts = [
        'prestashop_api_key' => 'encrypted',
    ];
}
