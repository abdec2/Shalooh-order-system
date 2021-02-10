<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDetails;

class OrderDetails1 extends Model
{
    use HasFactory;

    protected $table = 'order_details1s';


    public function OrderDetails()
    {
        return $this->belongsTo(OrderDetails::class, 'order_detail_id');
    }
}
