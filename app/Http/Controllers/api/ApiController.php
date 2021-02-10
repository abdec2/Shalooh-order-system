<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDetails;
use App\Models\OrderDetails1;

class ApiController extends Controller
{
    public function newOrder( Request $request )
    {
        
        if($request->hasHeader('x-wc-webhook-resource') && $request->header('x-wc-webhook-resource') !== 'order')
        {
            $response = [];
            $response['status_code'] = 400;
            $response['msg'] = 'Bad Request';
            return response()->json($response);
        }

        if($request->hasHeader('x-wc-webhook-event') && $request->header('x-wc-webhook-event') !== 'created')
        {
            $response = [];
            $response['status_code'] = 400;
            $response['msg'] = 'Bad Request';
            return response()->json($response);
        }

        $exist = OrderDetails::where('order_number', $request->number)->get();

        if( count($exist) > 0 )
        {
            $response = [];
            $response['status_code'] = 200;
            $response['msg'] = 'Order already Exist';
            return response()->json($response);

        } else {
            $order = new OrderDetails;
            $order->order_number = $request->number;
            $order->order_data = serialize($request->all());
            $order->shipping_method = $request->shipping_lines[0]['method_title'];
            $order->status = $request->status;
            $order->save();
            $orderDetail = new OrderDetails1;
            $order->OrderDetails1()->save($orderDetail);

            $response = [];
            $response['status_code'] = 201;
            $response['msg'] = 'Order created successfully';
            return response()->json($response);
            
        }

        
    }
}
