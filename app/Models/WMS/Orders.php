<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = ['order_number', 'shipping_carrier_id', 'order_date', 'payment_method', 'order_status_id', 'shipping_address'];

    function OrderStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}
