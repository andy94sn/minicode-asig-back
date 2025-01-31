<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class Category extends Model
{
    protected $fillable = [
        'token',
        'name',
        'slug',
        'image',
        'status',
        'order'
    ];

    protected $casts = [
        'token' => 'string',
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->token)) {
                $category->token = self::generateToken();
            }
        });
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
