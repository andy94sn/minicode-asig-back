<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission as Model;
use Ramsey\Uuid\Guid\Guid;

class Permission extends Model
{
    protected $fillable = ['name', 'description', 'guard_name', 'token'];

    protected $casts = [
        'token' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->token)) {
                $permission->token = self::generateToken();
                Log::info('Creating permission with token: ' . $permission->token);
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
