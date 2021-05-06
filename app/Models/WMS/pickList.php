<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\WMS\OrderAssignedUser;
use App\Models\WMS\Locations;
use App\Models\WMS\Bins;
use App\Models\WMS\Products;

class pickList extends Model
{
    use HasFactory;
    protected $table = 'pick_list';
    protected $primaryKey = 'id';

    protected $fillable = ['order_ass_user_id', 'location_id', 'bin_id', 'product_id', 'qty_picked', 'status'];

    protected $guard = [];

    function OrderAssignedUser()
    {
        return $this->hasOne(OrderAssignedUser::class, 'id');
    }

    function location()
    {
        return $this->hasOne(Locations::class, 'id');
    }

    function Bin()
    {
        return $this->hasOne(Bins::class, 'id');
    }

    function Product()
    {
        return $this->hasOne(Products::class, 'id');
    }

    
}
