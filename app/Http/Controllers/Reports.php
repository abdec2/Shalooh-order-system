<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class Reports extends Controller
{
    function index(Request $request)
    {
        $basicAuth['username'] = env('WOOCOMMERCE_CONSUMER_KEY');
        $basicAuth['password'] = env('WOOCOMMERCE_SECRET');
        $page = 1;
        $after = $request->fromDate."T00:00:00";
        $before = $request->toDate."T23:59:59";
        $totalRecords = [];
        do {
            $woocommerce_endpoint = "https://www.shalooh.com/wp-json/wc/v3/orders?after=".$after."&before=".$before."&per_page=10&page=".$page;
            $response = Http::withBasicAuth($basicAuth['username'], $basicAuth['password'])->get($woocommerce_endpoint);
            $response = json_decode($response);
            foreach($response as $order)
            {
                array_push($totalRecords, $order);
            }
            $page=$page+1;
        } while(count($response) !== 0);

        if(strtoupper($request->reportType) == strtoupper('standard'))
        {
            return view('reports')->with('totalRecords', $totalRecords);
        } 
        if(strtoupper($request->reportType) == strtoupper('tax'))
        {
            return view('reports')->with('taxRecords', $totalRecords);
        }

        
    }
}
