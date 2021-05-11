<?php

namespace App\Models\WMS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WMS\Orders;
use App\Models\WMS\pickList;


class OrderAssignedUser extends Model
{
    use HasFactory;
    protected $table = 'order_assigned_users';
    protected $primaryKey = 'id';


    protected $fillable  = ['user_id', 'order_id','pick_tray','status', 'created_at','updated_at'];
    protected $guarded = [];


    function User(){
        return $this->belongsTo(User::class, 'user_id');
    }

    function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    function pickList()
    {
        return $this->hasMany(pickList::class, 'order_ass_user_id');
    }
}
