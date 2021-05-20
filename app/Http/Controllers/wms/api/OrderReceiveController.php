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
            dd('in'); 
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }



    } // function ends here
}
