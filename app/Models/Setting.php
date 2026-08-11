<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'address',
        'phone',
        'email',
        'working_hours',
        'facebook_url',
        'instagram_url',
    ];
}