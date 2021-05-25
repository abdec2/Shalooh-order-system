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
        $orders = DB::table('orders')->select('orders.*', 'shipping_carrier.shipping_carrier')->join('order_status', 'orders.order_status_id','=','order_status.id')->where('order_status.status', strtoupper('Pending'))
        ->join('shipping_carrier', 'shipping_carrier.id','=','orders.shipping_carrier_id')
        ->paginate(10);
        // dd($orders);
        return view('wms/pendingOrders', compact('orders'));
    } // function ends here

    function listProcessingOrders(Request $request)
    {
        $orders = DB::table('orders')->select('orders.*','shipping_carrier.shipping_carrier')->join('order_status', 'orders.order_status_id','=','order_status.id')->where(strtoupper('order_status.status'), '=', strtoupper('Processing'))
        ->join('shipping_carrier', 'shipping_carrier.id','=','orders.shipping_carrier_id')
        ->paginate(10);

        $users = User::all();

        return view('wms/processOrders', ['orders'=>$orders, 'users'=>$users]);
    } // function ends here

    function listShippedOrders(Request $request)
    {
        $orders = DB::table('orders')->select('orders.*','shipping_carrier.shipping_carrier')->join('order_status', 'orders.order_status_id','=','order_status.id')->where(strtoupper('order_status.status'), '=', strtoupper('Shipped'))
        ->join('shipping_carrier', 'shipping_carrier.id','=','orders.shipping_carrier_id')
        ->paginate(10);

        $users = User::all();

        return view('wms/shippedOrders', ['orders'=>$orders, 'users'=>$users]);
    } // function ends here
}
