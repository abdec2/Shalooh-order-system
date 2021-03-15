<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Shipping\FedEx;
use App\Classes\Shipping\FedExRate;



class FedExController extends Controller
{
    public function create(Request $request)
    {
        $FedEx = new FedEx($request);
        $result = $FedEx->createShipment();
        return response()->json($result);
    } // function ends here

    public function getRates(Request $request)
    {
        $FedExRates = new FedExRate($request);
    }
    
} // class ends here


