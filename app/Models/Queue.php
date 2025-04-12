<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['name'];

    public function __construct(array $attributes = [])
    {
        dd('Queue model loaded');
        parent::__construct($attributes);
    }
}
