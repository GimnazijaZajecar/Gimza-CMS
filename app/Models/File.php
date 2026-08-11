<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'title',
        'file_name',
        'url',
        'uploaded_by_id',
        'uploaded_date',
        'is_active',
        'file_type',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}