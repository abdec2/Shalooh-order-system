<?php

namespace App\Http\Controllers\wms\orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Orders;

class OrderController extends Controller
{
    function listPendingOrders(Request $request)
    {
        $orders = Orders::with(['OrderStatus' => function($q) {
            $q->where('status', 'Pending'); 
        }])->paginate(10);

        return view('wms/pendingOrders', compact('orders'));
    } // function ends here
}
