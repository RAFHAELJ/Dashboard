<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')->withPivot('permission_id')->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withTimestamps();
    }
}
