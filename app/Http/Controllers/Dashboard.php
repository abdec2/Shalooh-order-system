<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetails;


class Dashboard extends Controller
{
    function index(Request $request){
        $orders = OrderDetails::with('OrderDetails1')->whereHas('OrderDetails1', function($q){
            $q->where('read', 0);
        })->get();
        return view('dashboard')->with(array('orders'=>$orders));
    } // function ends here
}
