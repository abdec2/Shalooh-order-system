<?php

namespace App\Classes\Shipping;

use App\Classes\Shipping\SMSA;
use App\Classes\Shipping\FedEx;
use App\Classes\Shipping\KeyArabia;

class Shipment {
    
    private $shipping_method = NULL;
    private $order = NULL;
    private $result = [];

    public function __construct($method, $orderArray) 
    {
        $this->shipping_method = $method;
        $this->order = $orderArray;
    } // constructor ends here 

    public function addShip()
    {
        if( strtoupper($this->shipping_method) == strtoupper('Standard') || strtoupper($this->shipping_method) == strtoupper('SMSA Express') ) {
            $SMSA = new SMSA;
            $response = $SMSA->Generate_SMSA_Waybill_Number_With_File($this->order);
            $this->result['file'] = base64_decode($response['AwbFile']);
            $this->result['tracking_number'] = $response['AwbNumber'];
        }
        else if( strtoupper($this->shipping_method) == strtoupper('FedEX') ) {
            // $FedEx = new FedEX($this->order);
            // $label = $FedEx->createShipment();
            // incomplete
        }
        else if( strtoupper($this->shipping_method) == strtoupper('TNT Express') ) {
            // incomplete
        }
        else {
            $keyArabia = new KeyArabia($this->order);
            $response = $keyArabia->place_order();
            $this->result['tracking_number'] = $response->body();

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [101, 152],
                'orientation'=>'p',
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-top' => 0, 
                'margin-bottom' => 0,
                'margin_header' =>0,
                'margin_footer' => 0
            ]);
            $KeyArabiaArray = $this->order;
            $KeyArabiaArray['task_id'] = $this->result['tracking_number'];
            $html = \View::make('template.shippingLabel')->with('data', $KeyArabiaArray);
            $html = $html->render();
            $mpdf->WriteHTML($html);
            $this->result['file']=$mpdf->Output('', 'S');
        }

        return $this->result;

    } // function ends here

} // class ends here