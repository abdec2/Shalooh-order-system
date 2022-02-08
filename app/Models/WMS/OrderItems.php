<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WMS\Orders;
use App\Models\WMS\Products;

class OrderItems extends Model
{
    use HasFactory;

    function order() {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    function product() {
        return $this->belongsTo(Products::class, 'product_id');
    }


}
