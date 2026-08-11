<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'category_id',
        'created_by_id',
        'updated_by_id',
        'is_featured',
        'is_active',
        'status',
        'featured_image_id',
        'event_date',
        'event_location',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function featuredImage()
    {
        return $this->belongsTo(File::class, 'featured_image_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}