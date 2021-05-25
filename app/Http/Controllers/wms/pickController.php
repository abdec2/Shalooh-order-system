<?php

namespace App\Http\Controllers\wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WMS\OrderAssignedUser;

class pickController extends Controller
{
    function index(Request $request)
    {
        $UserAssignedOrders = OrderAssignedUser::where('user_id', $request->user()->id)->where('status', 'pending')
                                ->with(['order' => function($q){
                                    $q->with('OrderStatus');
                                }])
                                ->whereHas('order', function($q){
                                    $q->whereHas('OrderStatus', function($que){
                                        $que->where('status', 'Processing');
                                    });
                                })
                                ->get();
        
        // dd($UserAssignedOrders);

        return view('wms/pickPack', ['data'=>$UserAssignedOrders]);
    }
}
