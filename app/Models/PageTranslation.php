<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $table = 'page_translations';

    protected $fillable = [
        'page_id',
        'language',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    public $timestamps = true;

    public function getTranslatedTitleAttribute(): string
    {
        return ucfirst($this->title);
    }

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
