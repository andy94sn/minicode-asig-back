<?php

namespace App\Models;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Guid\Guid;

class Section extends Model
{
    use SoftDeletes;
    protected $fillable = ['token', 'slug', 'name', 'page_id', 'order', 'status'];

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function components(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Component::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            if (empty($component->token)) {
                $section->token = self::generateToken();
            }
        });
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = HelperService::slugify($value);
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
