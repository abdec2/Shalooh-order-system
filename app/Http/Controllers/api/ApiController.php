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
            $orderWeight = 0;
            $orderVolWeight = 0;
            
            foreach($request->meta_data as $item){
                if($item['key'] == '_cart_volweight')
                {
                    $orderVolWeight = $item['value'];
                }
                if($item['key'] == '_cart_weight')
                {
                    $orderWeight = $item['value'];
                }
            }

            $order = new OrderDetails;
            $order->order_number = $request->number;
            $order->order_data = serialize($request->all());
            $order->shipping_method = $request->shipping_lines[0]['method_title'];
            $order->status = $request->status;
            $order->save();
            $orderDetail = new OrderDetails1;
            $orderDetail->total_weight = $orderWeight;
            $orderDetail->total_vol_weight = $orderVolWeight;
            $order->OrderDetails1()->save($orderDetail);

            $response = [];
            $response['status_code'] = 201;
            $response['msg'] = 'Order created successfully';
            return response()->json($response);
        }

        
    } // function ends here 

    public function updateOrder( Request $request ) 
    {
        if($request->hasHeader('x-wc-webhook-resource') && $request->header('x-wc-webhook-resource') !== 'order')
        {
            $response = [];
            $response['status_code'] = 400;
            $response['msg'] = 'Bad Request';
            return response()->json($response);
        }

        if($request->hasHeader('x-wc-webhook-event') && $request->header('x-wc-webhook-event') !== 'updated')
        {
            $response = [];
            $response['status_code'] = 400;
            $response['msg'] = 'Bad Request';
            return response()->json($response);
        }

        $exist = OrderDetails::where('order_number', $request->number)->get();

        if( count($exist) > 0 )
        {
            $order = OrderDetails::find($exist[0]->id);
            $order->order_number = $request->number;
            $order->order_data = serialize($request->all());
            $order->shipping_method = $request->shipping_lines[0]['method_title'];
            $order->status = $request->status;
            $order->save();

            $response = [];
            $response['status_code'] = 200;
            $response['msg'] = 'Order Updated successfully';
            return response()->json($response);

            

        } else {
            $response = [];
            $response['status_code'] = 404;
            $response['msg'] = 'Order not exist';
            return response()->json($response);
        }


    } // function ends here



} // class ends here
