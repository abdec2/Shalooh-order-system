<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Shipping\FedEx;



class FedExController extends Controller
{
    public function create(Request $request)
    {
        $FedEx = new FedEx($request);
        $result = $FedEx->createShipment();
        return response()->json($result);
    } // function ends here
    
} // class ends here


