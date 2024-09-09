<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginCustomization extends Model
{
    protected $table = 'login_customizations';
    protected $fillable = [
        'top_image', 'background_type', 'background_value', 'login_button_background',
        'login_button_color', 'login_button_shape', 'login_button_text', 'social_logins',
        'login_method', 'password_method', 'registration_fields', 'faq'
    ];

    protected $casts = [
        'social_logins' => 'array',
        'login_method' => 'array',
        'password_method' => 'array',
        'registration_fields' => 'array',
    ];
}

