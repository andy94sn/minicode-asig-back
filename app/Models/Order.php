<?php

namespace App\Models;

use App\Enums\InsuranceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Guid\Guid;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'token',
        'name',
        'email',
        'code',
        'certificate',
        'person_type',
        'validity',
        'phone',
        'type',
        'status',
        'price',
        'refund',
        'info',
        'contract',
        'policy',
        'demand'
    ];

    protected $casts = [
        'info' => 'array'
    ];

    /**
     * Insurance Types
     */
    public function getInsuranceTypeAttribute($value): InsuranceType
    {
        return InsuranceType::from($value);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->token)) {
                $order->token = self::generateToken();
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
