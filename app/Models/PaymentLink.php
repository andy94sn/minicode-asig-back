<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentLink extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'order_id',
        'status',
        'admin_id'
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
}
