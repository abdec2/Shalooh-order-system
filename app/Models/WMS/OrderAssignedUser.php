<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WMS\Orders;


class OrderAssignedUser extends Model
{
    use HasFactory;
    protected $table = 'order_assigned_users';
    protected $primaryKey = 'id';


    protected $fillable  = ['user_id', 'order_id','pick_tray','status', 'created_at','updated_at'];
    protected $guarded = [];


    function User(){
        return $this->hasOne(User::class, 'id');
    }

    function order()
    {
        return $this->hasOne(Orders::class, 'id');
    }
}
