<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Guid\Guid;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Model
{
    use HasRoles;

    protected $table = 'admins';

    protected $fillable = [
        'token',
        'email',
        'password',
        'name',
        'status',
        'is_super'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'token' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            if (empty($admin->token)) {
                $admin->token = self::generateToken();
            }
        });
    }

    /**
     * Crypt Data
     *
     * @param string $value
     */
    public function setPasswordAttribute(string $value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    private static function generateToken(): string
    {
        do {
            $token = (string) Guid::uuid4();
        } while (self::where('token', $token)->exists());

        return $token;
    }

}
