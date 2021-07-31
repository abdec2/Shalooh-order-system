<?php

namespace App\Http\Controllers\wms\ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Orders;
use App\Models\WMS\OrderStatus;
use App\Models\WMS\OrderAssignedUser;
use App\Models\WMS\pickList;
use App\Models\WMS\countriesModel;
use App\Models\WMS\citiesModel;
use App\Models\WMS\Inventory;
use App\Models\WMS\Products;

use App\Classes\Shipping\Shipment;
use App\Classes\livesite\WoocommerceClass;

use Illuminate\Support\Carbon;
use App\Exceptions\OrderAlreadyAssigned;
use App\Exceptions\TrayAlreadyInUse;
use App\Exceptions\TrayNotAssigned;
use App\Exceptions\NotAnArray;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;



class AjaxController extends Controller
{
    function waveOrder(Request $request)
    {
        try{
            $order_ids = json_decode($request->order_id);
            $statusId = OrderStatus::where('status', 'Processing')->get();

            foreach($order_ids as $orderId)
            {
                $order = Orders::find($orderId);
                $order->order_status_id = $statusId[0]->id;
                $order->save();
            }
            $data['type']='success';
            $data['msg'] = 'Order Processed..';
            return response()->json($data);

        } catch(\Exception $e){
            $data['type']='error';
            $data['msg'] = $e->getMessage();
            return response()->json($data);
        }
    } // function ends here


    function fulfillment(Request $request)
    {
     
        try
        {
            $orders = json_decode($request->selectedOrders);
            $user_id= $request->picker;
            $data = [];
            foreach($orders as $order)
            {
                $CheckOrderAssigned = OrderAssignedUser::where('order_id', $order)->get();
                if(count($CheckOrderAssigned) > 0)
                {
                    throw new OrderAlreadyAssigned(); // throw exception that the order is already assigned to the user
                }
                $data1 = [
                    'user_id' => $user_id,
                    'order_id' => $order, 
                    'pick_tray' => NULL,
                    'status' => 'pending',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
        
                array_push($data, $data1);
                
            }
            OrderAssignedUser::insert($data);

            $success['msg'] = 'Order has been assigned..';
            $success['type'] = 'success';

            return response()->json($success);

        } catch (OrderAlreadyAssigned $e)
        {
            $error['type']='error';
            $error['msg'] = 'Order already assigned';
            return response()->json($error);
        } 
        catch (\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        } 
       
    } // function ends here

    // function to fetch user assigned order ready with status not shipped and delivered

    function fetchUserAssignedOrders(Request $request) {
        try {

            $UserAssignedOrders = OrderAssignedUser::where('user_id', $request->user()->id)->where('status', 'pending')
                                ->with(['order' => function($q){
                                    $q->with('OrderStatus');
                                }])
                                ->whereHas('order', function($q){
                                    $q->whereHas('OrderStatus', function($que){
                                        $que->where('status', 'Processing');
                                    });
                                })
                                ->get();

            return response()->json($UserAssignedOrders);

        } catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    // function to assign tray to an order

    function AssignTray(Request $request)
    {
        try
        {
            $isExist = OrderAssignedUser::where( 'status', 'pending' )->where( 'pick_tray', $request->tray)->where('user_id', $request->user()->id)->get();

            if( count($isExist) > 0 )
            {
                throw new TrayAlreadyInUse(); // throw exception when tray is already in use
            } else 
            {
                $OrderAssignTray = OrderAssignedUser::find($request->order_assigned_users_id);
                $OrderAssignTray->pick_tray = $request->tray;
                $OrderAssignTray->save();
    
                $success['type']='refresh';
                $success['msg'] = '/wms/pick';
                return response()->json($success);
            }
        } 
        catch(TrayAlreadyInUse $e)
        {
            $error['type']='error';
            $error['msg'] = 'Tray already in use';
            return response()->json($error);
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    // start pick n pack process
    function pickNpackInit(Request $request)
    {   
        try{
            $UserAssignedOrders = OrderAssignedUser::where('user_id', $request->user()->id)->where('status', 'pending')
                                ->where('pick_tray', '!=', NULL)
                                ->with(['order' => function($q){
                                    $q->with('OrderStatus');
                                }])
                                ->whereHas('order', function($q){
                                    $q->whereHas('OrderStatus', function($que){
                                        $que->where('status', 'Processing');
                                    });
                                })
                                ->get();

            if( count($UserAssignedOrders) == 0 ) {
                throw new TrayNotAssigned();
            }

            $productsArray = [];
            
            foreach($UserAssignedOrders as $item){

                $products = DB::table('order_items')->select('products.label', 'products.sku', 'products.image_path','bins.*', 'locations.location', 'inventory.quantity', 'order_items.order_qty', DB::raw("'$item->pick_tray' as order_position"), DB::raw("$item->id as order_ass_user_id"))
                            ->join('products', 'products.id', '=', 'order_items.product_id')
                            ->join('bins', function($join){
                                $join->on('bins.product_id','=', 'order_items.product_id');
                            })
                            ->join('locations', 'locations.id', '=', 'bins.location_id')
                            ->join('inventory', 'inventory.bin_id', '=', 'bins.id')
                            ->where('order_items.order_id', '=', $item->order_id)
                            ->where('inventory.quantity', '>', 'order_items.order_qty')
                            ->where('locations.location_category_id', '!=', 11)
                            ->orderBy('bins.tag_number', 'asc')
                            ->groupBy('sku')
                            ->get();
                array_push($productsArray, $products->all());
            }
            $Collection = collect($productsArray)->flatten()->sortBy('tag_number')->values();
            return response()->json($Collection);

        } 
        catch(TrayNotAssigned $e){
            $error['type']='error';
            $error['msg'] = 'Tray is not assigned to any order';
            return response()->json($error);
        }
        catch(\Exception $e){
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    // function to flatten the multidimensional array.. but we are not using this method. we are using collections for this operation.
    function array_flatten($array)
    {
        if(!is_array($array))
        {
            throw new NotAnArray('Passing parameter to a function is not an array');
        }

        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, $this->array_flatten($value)); 
            } 
            else { 
                $result[$key] = $value; 
            } 
        } 
        return $result;
    } // function ends here

    // function to add picklist
    function addPickList(Request $request)
    {
        try
        {
            
            $item = $request->item;

            foreach($item as $key => $value)
            {
                $item[$key]['status'] = $request->status;
                $item[$key]['created_at'] = Carbon::now()->toDateTimeString();
                $item[$key]['updated_at'] = Carbon::now()->toDateTimeString();
                OrderAssignedUser::where('id',$value['order_ass_user_id'])->update(['status' => 'picked']);
                $inventory = Inventory::where('bin_id', $value['bin_id'])->get();
                $inventoryUpdate = Inventory::find($inventory[0]->id);
                $inventoryUpdate->quantity = (int)$inventoryUpdate->quantity - (int)$value['qty_picked'];
                $inventoryUpdate->save();
            }

            pickList::insert($item);
            
            $success['type']='success';
            $success['msg'] = 'item picked';
            return response()->json($success);

        }
        catch(\Exception $e){
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }

    } // function ends here

    // function to get Order details for the ship orders popup
    function getShipOrderDetails(Request $request)
    {
        try
        {
            $orderDetail = Orders::select('orders.*', 'products.*', 'bins.*', 'shipping_carrier.*','pick_list.*')
                            ->join('shipping_carrier','shipping_carrier.id','=','orders.shipping_carrier_id')
                            ->join('order_assigned_users', 'order_assigned_users.order_id', '=', 'orders.id')
                            ->join('pick_list', 'pick_list.order_ass_user_id','=','order_assigned_users.id')
                            ->join('products','products.id','=','pick_list.product_id')
                            ->join('bins','bins.id','=','pick_list.bin_id')
                            ->where('orders.id', $request->orderID)
                            ->get();

            if(count($orderDetail) == 0)
            {
                throw new \Exception('No details found');
            }

            $orderData = unserialize($orderDetail[0]->order_data);
            $orderDetail[0]['orderData1'] = $orderData;

            return response()->json($orderDetail);

        }
        catch(\Exception $e){
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    // function to save customer details by order id

    function save_customer_detail(Request $request)
    {
        try{
            $updateOrder = Orders::find($request->orderID);
            if(!$updateOrder)
            {
                throw new \Exception('Order not found.');
            }

            $validated = $request->validate([
                'customerName' => 'required|max:255',
                'customerContact' => 'required|max:50',
                'shipping_address1' => 'required|max:1000',
                'shipping_address2' => 'max:1000',
                'state' => 'max:255',
                'postal' => 'max:255',
                'city' => 'required|max:255',
                'country' => 'required|max:255'
            ]);


            $updateOrder->customer_name = $validated['customerName'];
            $updateOrder->customer_contact = $validated['customerContact'];
            $updateOrder->shipping_address1 = $validated['shipping_address1'];
            $updateOrder->shipping_address2 = $validated['shipping_address2'];
            $updateOrder->state = $validated['state'];
            $updateOrder->postal = $validated['postal'];
            $updateOrder->city = $validated['city'];
            $updateOrder->country = $validated['country'];
            $updateOrder->save();

            $validated['type']='success';
            return response()->json($validated);

        } catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    // function to save shipping details of an order by using orderid
    function save_shipping_detail(Request $request)
    {
        try
        {
            $updateOrder = Orders::find($request->orderID);
            if(!$updateOrder)
            {
                throw new \Exception('Order not found.');
            }

            $validated = $request->validate([
                'totalVolWeight' => 'required|numeric',
                'shipping_package_size' => 'required',
                'package_length' => 'required|numeric',
                'package_width' => 'required|numeric',
                'package_height' => 'required|numeric'
            ]);


            $updateOrder->total_vol_weight = $validated['totalVolWeight'];
            $updateOrder->package_size = $validated['shipping_package_size'];
            $updateOrder->package_length = $validated['package_length'];
            $updateOrder->package_width = $validated['package_width'];
            $updateOrder->package_height = $validated['package_height'];
            $updateOrder->save();

            $validated['type']='success';
            return response()->json($validated);
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    //function to get countries from database

    function getCountries(Request $request)
    {
        try
        {
            $countries = countriesModel::all();

            return response()->json($countries);
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

     //function to get cities by country code from db
     function getCitiesByCountry(Request $request)
     {
         try
         {
             $cities = citiesModel::select('wp_cities.*')
                        ->join('wp_countries', 'wp_countries.id', '=', 'wp_cities.country_id')
                        ->where('wp_countries.country_code', $request->country)
                        ->get();
 
             return response()->json($cities);
         }
         catch(\Exception $e)
         {
             $error['type']='error';
             $error['msg'] = $e->getMessage();
             return response()->json($error);
         }
     } // function ends here

     // function to create label and ship order
     function createLabelAndShipOrder(Request $request)
     {
        try
        {
            
            $order = Orders::where('id', $request->orderID)->with('shipping_carrier')->get();
            $orderData = unserialize($order[0]->order_data);

            $orderArray = [];
            $orderArray['Order_ID'] = $order[0]->order_number;
            $orderArray['shipping_method'] = $order[0]->shipping_carrier->shipping_carrier;
            $orderArray['customer_name'] = $order[0]->customer_name;
            $orderArray['shipping_address1'] = $order[0]->shipping_address1;
            $orderArray['shipping_address2'] = $order[0]->shipping_address2;
            $orderArray['city'] = $order[0]->city;
            $orderArray['state'] = ($order[0]->state !== null) ? $order[0]->state : '';
            $orderArray['postcode'] = ($order[0]->postal !== null) ? $order[0]->postal : '';
            $orderArray['country'] = $order[0]->country;
            $orderArray['phone'] = $order[0]->customer_contact;
            $orderArray['payment_method'] = $order[0]->payment_method;
            $orderArray['order_amount'] = $orderData['total'];
            $orderArray['email'] = $orderData['billing']['email'];
            $orderArray['orderweight'] = $order[0]->total_weight;
            $orderArray['orderVolweight'] = $order[0]->total_vol_weight;
            $orderArray['package_length'] = $order[0]->package_length;
            $orderArray['package_width'] = $order[0]->package_width;
            $orderArray['package_height'] = $order[0]->package_height;
            $orderArray['orderData'] = $orderData;

            if( $orderArray['package_length'] == null || $orderArray['package_width']  == null || $orderArray['package_height'] == null )
            {
                throw new \Exception('Please enter the Package dimensions.');
            }

            if(strtoupper($order[0]->shipping_carrier->shipping_carrier) !== strtoupper('TNT Express'))
            {
                $shipment = new Shipment($order[0]->shipping_carrier->shipping_carrier, $orderArray);
                $result = $shipment->addShip();

                // print_r($result);
                $OrderStatus = OrderStatus::where( strtoupper('status'), strtoupper('Shipped') )->get();
                $orderUpdate = Orders::find($request->orderID);
                $orderUpdate->tracking_no = $result['tracking_number'];
                $orderUpdate->order_status_id = $OrderStatus[0]->id;
                $orderUpdate->save();

                // WoocommerceClass::update_shipment_tracking_number($order[0]->shipping_carrier->shipping_carrier, $result['tracking_number'], $order[0]->order_number);

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
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
     } // function ends here

     function getProductInfo (Request $request) {
        try {
            $products = Products::where('id', $request->product_id)->with('AvailableStock')->with(['Bins' => function($q){    
                $q->with('Inventory');
            }])->get();

            return response()->json($products);
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
     } // function ends here
 

} // class ends here
