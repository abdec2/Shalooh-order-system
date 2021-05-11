<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\WMS\OrderAssignedUser;
use App\Models\WMS\ShippingCarriers;


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

    function order_assigned_user()
    {
        return $this->hasOne(OrderAssignedUser::class, 'order_id');
    }

    function shipping_carrier()
    {
        return $this->belongsTo(ShippingCarriers::class);
    }
}
