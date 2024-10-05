<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginCustomization extends Model
{
    protected $table = 'login_customizations';
    protected $fillable = [
        'layout_name',
        'top_type',
        'top_value',
        'background_type',
        'background_value',
        'login_button_text',
        'login_button_color',
        'login_button_shape',
        'login_method' ,
        'password_method',
        'input_width',
        'input_height' ,
        'input_color',
        'background_image',
        'elements' ,
        'region' ,
        'username' ,
    ];

    protected $casts = [
        'social_logins' => 'array',
        'login_method' => 'array',
        'password_method' => 'array',
        'registration_fields' => 'array',
    ];
}

