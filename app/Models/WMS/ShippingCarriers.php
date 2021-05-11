<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCarriers extends Model
{
    use HasFactory;
    protected $table = 'shipping_carrier';
    protected $primaryKey = 'id';
}
