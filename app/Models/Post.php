<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class Post extends Model
{
    protected $fillable = [
        'token',
        'name',
        'slug',
        'content',
        'author',
        'image',
        'tags',
        'status',
        'order',
        'category_id',
        'published_at'
    ];

    protected $casts = [
        'token' => 'string',
        'tags'  => 'array'
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->token)) {
                $post->token = self::generateToken();
            }
        });
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PostTranslation::class);
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
