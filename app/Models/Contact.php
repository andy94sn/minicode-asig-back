<?php

namespace App\Models;

use App\Services\HelperService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Guid\Guid;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';
    protected $fillable = ['token', 'name', 'page', 'email', 'phone', 'message'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($contact) {
            if (empty($contact->token)) {
                $contact->token = self::generateToken();
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

