<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $fillable = [
        'title',
        'body',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
