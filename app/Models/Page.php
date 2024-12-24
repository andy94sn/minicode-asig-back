<?php

namespace App\Models;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Guid\Guid;

class Page extends Model
{
    use SoftDeletes;
    protected $fillable = ['token', 'title', 'slug', 'status', 'type', 'order'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->token)) {
                $page->token = self::generateToken();
            }
        });
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }


    public function translations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = HelperService::slugify($value);
    }

    public function sections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Section::class);
    }
}
