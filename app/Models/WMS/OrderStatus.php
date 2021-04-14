<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WMS\Orders;

class OrderStatus extends Model
{
    use HasFactory;
    protected $table = 'order_status';
    protected $primaryKey = 'id';

    protected $fillable = ['status'];

    function Orders()
    {
        return $this->hasMany(Orders::class, 'order_status_id');
    }
}
