<?php 

namespace App\Classes\livesite;

use Illuminate\Support\Facades\Http;

class WoocommerceClass {

    public function __construct(){

    }

    static public function UpdateOrderAtWC($order, $orderID) // update order on Woocommerce
    {
        $endPoint = env('WOOCOMMERCE_ORDER_ENDPOINT').$orderID;
        $user = env('WOOCOMMERCE_CONSUMER_KEY');
        $pass = env('WOOCOMMERCE_SECRET');

        $response = Http::withBasicAuth($user, $pass)->withBody($order, 'application/json')->put($endPoint);

        if($response->failed())
        {
            $response->throw();
        }

    } // function ends here

    static public function update_shipment_tracking_number($shipment_method, $tracking_number, $order_number)
    {
        try{
            $endPoint = env('WOCOMMERCE_SHIPMENT_TRACK_ENDPOINT').$order_number.'/shipment-trackings';
            $user = env('WOOCOMMERCE_CONSUMER_KEY');
            $pass = env('WOOCOMMERCE_SECRET');

            $body = [];
            $body['custom_tracking_provider'] = $shipment_method;
            if(strtoupper($shipment_method) == strtoupper('SMSA Express'))
            {
                $body['custom_tracking_link'] = 'https://smsaexpress.com/trackingdetails?tracknumbers='.$tracking_number;
            }
            $body['tracking_number'] = $tracking_number;
            
            
            $response = Http::withBasicAuth($user, $pass)->withBody(json_encode($body), 'application/json')->post($endPoint);

            if($response->failed())
            {
                $response->throw();
            }

        } catch(\Exception $e){
            echo $e->getMessage();
        }
    }

} // class ends here