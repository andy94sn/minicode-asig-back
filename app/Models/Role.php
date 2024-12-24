<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role as Model;
use Spatie\Permission\Contracts\Role as RoleContract;
use Ramsey\Uuid\Guid\Guid;

class Role extends Model implements RoleContract
{
    protected $fillable = ['name', 'description', 'guard_name', 'token'];

    protected $casts = [
        'token' => 'string',
    ];


    public static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            Log::info('Creating permission with token: ' . $role->token);
            if (empty($role->token)) {
                $role->token = self::generateToken();
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
