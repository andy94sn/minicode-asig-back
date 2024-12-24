<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Guid\Guid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Component extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;
    protected $fillable = ['parent_id', 'token', 'title', 'type', 'key', 'content', 'status', 'order', 'section_id'];

    protected $casts = [
        'content' => 'array',
        'params' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($component) {
            if (empty($component->token)) {
                $component->token = self::generateToken();
            }
        });
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Component::class, 'parent_id');
    }


    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Component::class, 'parent_id');
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }
}
