<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginCustomization extends Model
{
    protected $connection = 'mysql';
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
        'social_networks',
        'caditens',
    ];

    protected $casts = [
        'social_logins' => 'array',
        'login_method' => 'array',
        'password_method' => 'array',
        'registration_fields' => 'array',
        'region' => 'integer',
        
    ];
    protected static function booted()
    {
        static::addGlobalScope('regiao', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->isAdmin()) {
                $builder->where('regiao', Auth::user()->regiao);
            }
        });
    }
}

