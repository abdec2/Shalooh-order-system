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
        if( strtoupper($this->shipping_method) == strtoupper('SMSA Express')) {
            $SMSA = new SMSA;
            $response = $SMSA->Generate_SMSA_Waybill_Number_With_File($this->order);
            // $this->result['file'] = base64_decode($response['AwbFile']);
            // $this->result['tracking_number'] = $response['AwbNumber'];
            $this->result = $response;
        }
        else if( strtoupper($this->shipping_method) == strtoupper('FedEx') ) {
            $FedEx = new FedEX($this->order);
            $label = $FedEx->createShipment();
            $this->result['file'] = $label['file'];
            $this->result['tracking_number'] = $label['tracking_number'];
            if(isset($label['COMM_INV']))
            {
                $this->result['COMM_INV'] = $label['COMM_INV'];
            }

        }
        else if( strtoupper($this->shipping_method) == strtoupper('TNT Express') ) {
            // incomplete
        }
        else if( strtoupper($this->shipping_method) == strtoupper('Shalooh Delivery') )
        {
            $this->result['tracking_number'] = $this->order['Order_ID'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [101, 152],
                'orientation'=>'p',
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-top' => 0, 
                'margin-bottom' => 0,
                'margin_header' =>0,
                'margin_footer' => 0, 
                'tempDir' => __DIR__.'/../../../public/temp',
            ]);
            $LDArray = $this->order;
            $logo = file_get_contents('./img/logo.png');
            $logo = 'data:image/png;base64,' . base64_encode($logo);
            $LDArray['logo'] = $logo;
            
            $LDArray['task_id'] = $this->result['tracking_number'];
            $html = \View::make('template.shaloohLabel')->with('data', $LDArray);
            $html = $html->render();
            $mpdf->WriteHTML($html);
            $this->result['file']=$mpdf->Output('', 'S');
        }
        else {
            $this->result['tracking_number'] = $this->order['Order_ID'];

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [101, 152],
                'orientation'=>'p',
                'margin-left' => 0,
                'margin-right' => 0,
                'margin-top' => 0, 
                'margin-bottom' => 0,
                'margin_header' =>0,
                'margin_footer' => 0,
                'tempDir' => __DIR__.'/../../../public/temp',
            ]);
            $LDArray = $this->order;
            $logo = file_get_contents('./img/logo.png');
            $logo = 'data:image/png;base64,' . base64_encode($logo);
            $LDArray['logo'] = $logo;
            
            $LDArray['task_id'] = $this->result['tracking_number'];
            $html = \View::make('template.vipDelivery')->with('data', $LDArray);
            $html = $html->render();
            $mpdf->WriteHTML($html);
            $this->result['file']=$mpdf->Output('', 'S');
            // $keyArabia = new KeyArabia($this->order);
            // $response = $keyArabia->place_order();
            // $this->result['tracking_number'] = $response->body();

            // $mpdf = new \Mpdf\Mpdf([
            //     'mode' => 'utf-8',
            //     'format' => [101, 152],
            //     'orientation'=>'p',
            //     'margin-left' => 0,
            //     'margin-right' => 0,
            //     'margin-top' => 0, 
            //     'margin-bottom' => 0,
            //     'margin_header' =>0,
            //     'margin_footer' => 0
            // ]);
            // $KeyArabiaArray = $this->order;
            // $KeyArabiaArray['task_id'] = $this->result['tracking_number'];
            // $html = \View::make('template.shippingLabel')->with('data', $KeyArabiaArray);
            // $html = $html->render();
            // $mpdf->WriteHTML($html);
            // $this->result['file']=$mpdf->Output('', 'S');
        }

        return $this->result;

    } // function ends here

} // class ends here