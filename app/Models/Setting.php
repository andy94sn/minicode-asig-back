<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'token',
        'key',
        'group',
        'values',
        'description',
        'status',
    ];


    protected $casts = [
        'values' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            if (empty($setting->token)) {
                $setting->token = self::generateToken();
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
}
