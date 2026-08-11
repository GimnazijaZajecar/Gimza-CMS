<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'gallery_id',
        'file_id',
        'order',
    ];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}