<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetails;
use App\Models\WpCountries;
use App\Models\WpCities;
use Illuminate\Support\Facades\Http;

use App\Classes\Shipping\Shipment;
use App\Classes\livesite\WoocommerceClass;


class Order extends Controller
{
    public function getOrder( Request $request, $id )
    {
        
        $orderDetails = OrderDetails::where('order_number', $id)->with('OrderDetails1')->get();
        if(count($orderDetails) > 0) {
            $orderData = unserialize($orderDetails[0]->order_data);
            
            
            $orderArray = [];
            $orderArray['Order_ID'] = $orderDetails[0]->order_number;
            $orderArray['shipping_method'] = $orderDetails[0]->shipping_method;
            $orderArray['status'] = $orderDetails[0]->status;
            $orderArray['statusOpts'] = ['pending'=>'Pending payment', 'processing' => 'Processing', 'in-transit' => 'In Transit','on-hold'=>'On hold','completed' => 'Completed','cancelled'=> 'Cancelled','refunded' => 'Refunded'];
            $orderArray['first_name'] = $orderData['shipping']['first_name'];
            $orderArray['last_name'] = $orderData['shipping']['last_name'];
            $orderArray['shipping_address1'] = $orderData['shipping']['address_1'];
            $orderArray['shipping_address2'] = $orderData['shipping']['address_2'];
            $orderArray['city'] = $orderData['shipping']['city'];
            $orderArray['state'] = $orderData['shipping']['state'];
            $orderArray['postcode'] = $orderData['shipping']['postcode'];
            $orderArray['country'] = $orderData['shipping']['country'];
            $orderArray['countryOpt'] = ['Bahrain' => 'BH', 'Saudia Arabia' => 'SA', 'Kuwait' => 'KW','Oman' => 'OM','Qatar' => 'QA','United Arab Emirates' => 'AE'];
            $orderArray['payment_method'] = $orderData['payment_method'];
            $orderArray['phone'] = $orderData['billing']['phone'];
            $orderArray['statusChangeReason'] = $orderDetails[0]->OrderDetails1->reason_status_change;
            $orderArray['trackingNo'] = $orderDetails[0]->OrderDetails1->tracking_no;
            $orderArray['totalWeight'] = $orderDetails[0]->OrderDetails1->total_weight;
            $orderArray['totalVolWeight'] = $orderDetails[0]->OrderDetails1->total_vol_weight;

            $orderUpdate = OrderDetails::find($orderDetails[0]->id);
            $orderUpdate->OrderDetails1()->update([
                'read' => 1,
                'updated_by' => $request->user()->id
            ]);

            return view('orders', $orderArray);
        }
        else {
            $res = [];
            $res['type'] = 'error';
            $res['msg'] = 'Record not found';
            return view('orders', $res);
        }


    } // function ends here

    public function formSubmit( Request $request ) 
    {
        $WpCountries = WpCountries::all();
        $countryOpt = [];
        foreach($WpCountries as $country)
        {
            $countryOpt[$country->country] = $country->country_code;
        }
        $orderDetails = OrderDetails::where('order_number', $request->orderNumber)->with('OrderDetails1')->get();
        if(count($orderDetails) > 0) {
            $orderData = unserialize($orderDetails[0]->order_data);
            $cities = WpCities::join('wp_countries', 'wp_cities.country_id','=','wp_countries.id')->select('wp_cities.*')->where('wp_countries.country_code', '=',$orderData['shipping']['country'])->get();
            $cityOpt = [];
            foreach($cities->all() as $city)
            {
                $cityOpt[$city->cities] = $city->cities;
            }            

            $orderArray = [];
            $orderArray['Order_ID'] = $orderDetails[0]->order_number;
            $orderArray['Order_Key'] = $orderData['order_key'];
            $orderArray['shipping_method'] = $orderDetails[0]->shipping_method;
            $orderArray['status'] = $orderDetails[0]->status;
            $orderArray['statusOpts'] = ['pending'=>'Pending payment', 'processing' => 'Processing', 'in-transit' => 'In Transit','on-hold'=>'On hold','completed' => 'Completed','cancelled'=> 'Cancelled','refunded' => 'Refunded'];
            $orderArray['first_name'] = $orderData['shipping']['first_name'];
            $orderArray['last_name'] = $orderData['shipping']['last_name'];
            $orderArray['shipping_address1'] = $orderData['shipping']['address_1'];
            $orderArray['shipping_address2'] = $orderData['shipping']['address_2'];
            $orderArray['city'] = $orderData['shipping']['city'];
            $orderArray['cityOpt'] = $cityOpt;
            $orderArray['state'] = $orderData['shipping']['state'];
            $orderArray['postcode'] = $orderData['shipping']['postcode'];
            $orderArray['country'] = $orderData['shipping']['country'];
            $orderArray['countryOpt'] = $countryOpt;
            $orderArray['payment_method'] = $orderData['payment_method'];
            $orderArray['phone'] = $orderData['billing']['phone'];
            $orderArray['statusChangeReason'] = $orderDetails[0]->OrderDetails1->reason_status_change;
            $orderArray['trackingNo'] = $orderDetails[0]->OrderDetails1->tracking_no;
            $orderArray['totalWeight'] = $orderDetails[0]->OrderDetails1->total_weight;
            $orderArray['totalVolWeight'] = $orderDetails[0]->OrderDetails1->total_vol_weight;

            return view('orders', $orderArray);
        }
        else {
            $res = [];
            $res['type'] = 'error';
            $res['msg'] = 'Record not found';
            return view('orders', $res);
        }
    }  // function ends here


    public function save_order( Request $request )
    {
        try 
        {
            $orderDetails = OrderDetails::where('order_number', $request->orderID)->get();
            if(count($orderDetails) > 0) {
                $orderData = unserialize($orderDetails[0]->order_data);
                $orderData['shipping']['address_1'] = $request->shipping_address1;
                $orderData['shipping']['address_2'] = $request->shipping_address2;
                $orderData['shipping']['city'] = $request->city;
                $orderData['shipping']['state'] = $request->state;
                $orderData['shipping']['postcode'] = $request->postal_code;
                $orderData['shipping']['country'] = $request->shipping_country;
                $orderData['billing']['phone'] = $request->contactNo;
                $orderData['status'] = $request->orderStatus;

                $WcData['status'] = $request->orderStatus;
                $WcData['shipping']['address_1'] = ($request->shipping_address1 !== null) ? $request->shipping_address1 : '';
                $WcData['shipping']['address_2'] = ($request->shipping_address2 !== null) ? $request->shipping_address2 : '';
                $WcData['shipping']['city'] = ($request->city !== null) ? $request->city : '';
                $WcData['shipping']['state'] = ($request->state !== null) ? $request->state : '';
                $WcData['shipping']['postcode'] = ($request->postal_code !== null ) ? $request->postal_code : '';
                $WcData['shipping']['country'] = ($request->shipping_country !== null ) ? $request->shipping_country : '';
                $WcData['billing']['phone'] = ($request->contactNo !== null ) ? $request->contactNo : '';
                
                WoocommerceClass::UpdateOrderAtWC( json_encode($WcData), $request->orderID );
                
                $orderData = serialize($orderData);

                $orderUpdate = OrderDetails::find($orderDetails[0]->id);

                $orderUpdate->order_data = $orderData;

                $orderUpdate->status = $request->orderStatus;

                $orderUpdate->save();

                $orderUpdate->OrderDetails1()->update([
                    'reason_status_change' => $request->statusChangeReason,
                    'total_vol_weight' => $request->totalVolWeight,
                    'package_size' => $request->shipping_package_size,
                    'package_length' => $request->package_length,
                    'package_width' => $request->package_width,
                    'package_height' => $request->package_height,
                    'updated_by' => $request->user()->id
                ]);


                $res = [];
                $res['type'] = 'success';
                $res['msg'] = 'Order Updated Successfully';

                return view('orders', $res);

            }
            else {
                $res = [];
                $res['type'] = 'error';
                $res['msg'] = 'Something went wrong';
                return view('orders', $res);
            }
        }
        catch( \Exception $e )
        {
            return $e;
        }

    } // function ends here


    function create_label( Request $request )
    {
        try
        {
            $orderDetails = OrderDetails::where('order_number', $request->orderID)->get();
            if(count($orderDetails) > 0) {

                $orderData = unserialize($orderDetails[0]->order_data);

                $orderArray = [];
                $orderArray['Order_ID'] = $orderDetails[0]->order_number;
                $orderArray['shipping_method'] = $orderDetails[0]->shipping_method;
                $orderArray['status'] = 'in-transit';
                $orderArray['first_name'] = $orderData['shipping']['first_name'];
                $orderArray['last_name'] = $orderData['shipping']['last_name'];
                $orderArray['shipping_address1'] = $request->shipping_address1;
                $orderArray['shipping_address2'] = $request->shipping_address2;
                $orderArray['city'] = $request->city;
                $orderArray['state'] = $request->state;
                $orderArray['postcode'] = $request->postal_code;
                $orderArray['country'] = $request->shipping_country;
                $orderArray['phone'] = $request->contactNo;
                $orderArray['payment_method'] = $orderData['payment_method'];
                $orderArray['order_amount'] = $orderData['total'];
                $orderArray['email'] = $orderData['billing']['email'];
                $orderArray['orderweight'] = $request->totalWeight;
                $orderArray['orderVolweight'] = $request->totalVolWeight;
                $orderArray['package_length'] = $request->package_length;
                $orderArray['package_width'] = $request->package_width;
                $orderArray['package_height'] = $request->package_height;
                $orderArray['orderData'] = $orderData;

                if(strtoupper($orderDetails[0]->shipping_method) !== strtoupper('TNT Express'))
                {
                    $shipment = new Shipment($orderDetails[0]->shipping_method, $orderArray);
                    $result = $shipment->addShip();

                    // print_r($result); die();

                    $orderUpdate = OrderDetails::find($orderDetails[0]->id);
                    $orderUpdate->OrderDetails1()->update([
                        'tracking_no' => $result['tracking_number'],
                        'updated_by' => $request->user()->id
                    ]);
                    
                    WoocommerceClass::update_shipment_tracking_number($orderDetails[0]->shipping_method, $result['tracking_number'], $orderDetails[0]->order_number);

                    if(isset($result['COMM_INV']))
                    {   
                        $zipper = new \Madnest\Madzipper\Madzipper;
                        $zipper->make('test.zip')->addString($orderArray['Order_ID'].'_AWB_'.date('Y-m-d H:i:s').'.pdf', $result['file'])->addString($orderArray['Order_ID'].'_COMM_INV_'.date('Y-m-d H:i:s').'.pdf', $result['COMM_INV'])->close();

                        $zipfile = file_get_contents('test.zip');

                        unlink('test.zip');

                        return response()->sendZip($zipfile);                        

                    } else {
                        return response()->attachment($result['file']);
                    }
                    
                }

            }

        }
        catch ( \Exception $e )
        {
            echo $e->getMessage();
        }
    } // function ends here

    function getAjaxCities(Request $request)
    {
        $cities = WpCities::join('wp_countries', 'wp_cities.country_id','=','wp_countries.id')->select('wp_cities.*')->where('wp_countries.country_code', '=',$request->shipping_country)->get();
        $cityOpt = '';
        foreach($cities->all() as $city)
        {
            $cityOpt .= '<option value="'.$city->cities.'">'.$city->cities.'</option>';
        }

        return response()->json($cityOpt);
    } // function ends here




} // class ends here 