<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Веза између странице и чланка, са редоследом приказа.
 */
class PageArticle extends Model
{
    protected $table = 'article_page';

    protected $fillable = [
        'page_id',
        'article_id',
        'order',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
