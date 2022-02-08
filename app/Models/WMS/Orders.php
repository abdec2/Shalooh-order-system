<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\WMS\OrderAssignedUser;
use App\Models\WMS\ShippingCarriers;
use App\Models\WMS\OrderItems;


class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $primaryKey = 'id';

    protected $fillable = ['order_number','customer_name','customer_contact', 'order_date', 'shipping_carrier_id', 'order_date','payment_method', 'order_status_id', 'shipping_address1', 'city', 'country'];

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

    function orderItems() {
        return $this->hasMany(OrderItems::class, 'order_id');
    }
}
