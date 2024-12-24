<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = 'refresh_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'revoked',
        'user_agent',
        'ip_address',
        'last_used_at',
        'scope',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
