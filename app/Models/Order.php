<?php

namespace App\Models;

use App\Enums\InsuranceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
