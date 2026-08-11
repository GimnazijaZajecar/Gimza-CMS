<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function staff()
    {
        return $this->belongsToMany(User::class, 'staff_subject', 'subject_id', 'staff_id');
    }
}