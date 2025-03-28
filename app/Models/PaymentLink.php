<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLink extends Model
{

    protected $fillable = [
        'order_id',
        'status',
        'admin_id',
        'code',
        'certificate',
        'trailer_id',
        'vehicle_data',
        'vehicle_owner',
        'vehicle_insured',
        'name'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public static function boot()
    {
        parent::boot();

        // static::creating(function ($order) {
        //     if (empty($order->token)) {
        //         // $order->token = self::generateToken();
        //     }
        // });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function agent()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
