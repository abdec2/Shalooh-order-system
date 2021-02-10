<?php

namespace App\Classes\Shipping;

use Illuminate\Support\Facades\Http;

class KeyArabia {

    public $base_url = 'http://trypkg.com';
    public $merchant_id = 136;
    public $order_id = NULL;
    public $payment_type = NULL;
    public $order_amount = NULL;
    public $customer_name = NULL;
    public $customer_phone = NULL;
    public $address = NULL;
    public $email = NULL;

    public function __construct($order) {
        $this->order_id = $order['Order_ID'];
        $this->payment_type = $order['payment_method'];
        $this->order_amount = $order['order_amount'];
        $this->customer_name = $order['first_name'].' '.$order['last_name'];
        $this->customer_phone = $order['phone'];
        $this->address = $order['shipping_address1'].' '.$order['shipping_address2'];
        $this->email = $order['email'];
        
    } // function ends here



    public function place_order()
    {
        try {
            $endpoint = 'http://204.48.23.96/allianz.php';
        
            $response = Http::post($endpoint, [
                'merchant_id' => $this->merchant_id,
                'customer_name' => $this->customer_name,
                'payment_type' => $this->payment_type,
                'amount' => $this->order_amount,
                'customer_address' => $this->address,
                'phone' => $this->customer_phone,
                'email' => $this->email,
                'orderid' => $this->order_id,
            ]);
            
            if($response->failed())
            {
                $response->throw();
            }

            return $response;
        } catch (\Exception $e)
        {
            echo $e->getMessage();
        }

    } // function ends here

    static public function check_order_status($task_id)
    {
        try {
            $endpoint = 'trypkg.com/order_status/'.$task_id;

            $response = Http::get($endpoint);

            if($response->failed())
            {
                $response->throw();
            }

            return $reponse;
        } catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    } // function ends here

} // Class ends here