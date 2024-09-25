<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions')->withPivot('page_id')->withTimestamps();
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'user_permissions', 'permission_id', 'page_id')
                    ->withTimestamps();
    }
}
