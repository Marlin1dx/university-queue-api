<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    // Определяем заполняемые атрибуты
    protected $fillable = ['name'];

    /**
     * Связь "многие ко многим" с пользователями (через таблицу 'queue_user').
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'queue_user')
                    ->withPivot('position', 'status')
                    ->withTimestamps();
    }

    /**
     * Связь с таблицей 'queue_user' для получения позиции и статуса.
     */
    public function queueUsers()
    {
        return $this->hasMany(QueueUser::class);
    }

    // Убираем dd() в конструкторе, чтобы модель могла загружаться без остановки
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}

