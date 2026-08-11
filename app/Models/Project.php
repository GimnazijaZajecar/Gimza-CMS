<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'external_url',
        'is_active',
        'image_id',
    ];

    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }
}