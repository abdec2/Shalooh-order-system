<?php

namespace App\Http\Controllers\wms\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderReceiveController extends Controller
{
    // function handles api to receive orders from shalooh wordpress website
    function OrderReceive(Request $request)
    {
        try
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
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }



    } // function ends here
}
