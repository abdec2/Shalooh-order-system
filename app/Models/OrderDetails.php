<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetails1;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = ['order_number', 'order_data', 'shipping_method', 'status'];

    public function OrderDetails1()
    {
        return $this->hasOne(OrderDetails1::class, 'order_detail_id');
    } 
}
