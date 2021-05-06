<?php

namespace App\Http\Controllers\wms\orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Orders;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class OrderController extends Controller
{
    function listPendingOrders(Request $request)
    {
        $orders = DB::table('orders')->select('orders.*')->join('order_status', 'orders.order_status_id','=','order_status.id')->where(strtoupper('order_status.status'), '=', strtoupper('Pending'))->paginate(10);
        // dd($orders);
        return view('wms/pendingOrders', compact('orders'));
    } // function ends here

    function listProcessingOrders(Request $request)
    {
        $orders = DB::table('orders')->select('orders.*')->join('order_status', 'orders.order_status_id','=','order_status.id')->where(strtoupper('order_status.status'), '=', strtoupper('Processing'))->paginate(10);

        $users = User::all();

        return view('wms/processOrders', ['orders'=>$orders, 'users'=>$users]);
    } // function ends here
}
