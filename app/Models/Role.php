<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Связь с пользователями
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Связь с разрешениями
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
}
