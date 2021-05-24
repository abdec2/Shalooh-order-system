<?php

namespace App\Http\Controllers\wms\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WMS\ShippingCarriers;
use App\Models\WMS\Orders;
use App\Models\WMS\OrderItems;
use App\Models\WMS\AvailableStocks;
use App\Models\WMS\HoldStock;
use App\Models\WMS\OrderStatus;
use App\Models\WMS\Products;
use App\Models\WMS\ApiError;

class OrderReceiveController extends Controller
{
    // function handles api to receive orders from shalooh wordpress website
    function OrderReceive(Request $request)
    {
        try
        {
            // getting shipping carrier id from db
            $shippingCarrier =  ShippingCarriers::where('shipping_method', $request->shipping_lines[0]['method_title'])->get();
            
            // getting order status id from db
            $orderStatus = OrderStatus::where(strtoupper('status'), strtoupper('pending'))->get();
            if( count($orderStatus) == 0 )
            {
                throw new \Exception('Order status not found');
            }

            $orderExist = Orders::where('order_number', $request->id)->get();
            if( count($orderExist) > 0 )
            {
                throw new \Exception('Order already exist');
            }

            $orderWeight = 0;
            $orderVolWeight = 0;
            
            // fetching order weight and order volumetric weight from the meta data of the wordpress order data
            foreach($request->meta_data as $item){
                if($item['key'] == '_cart_volweight')
                {
                    $orderVolWeight = $item['value'];
                }
                if($item['key'] == '_cart_weight')
                {
                    $orderWeight = $item['value'];
                }
            }
            // inserting data to the database order table
            $order = new Orders;
            $order->order_number = $request->id;
            $order->customer_name = $request->billing['first_name']. ' ' . $request->billing['last_name'];
            $order->customer_contact = $request->billing['phone'];
            $order->order_data = serialize($request->all());
            $order->shipping_carrier_id = ( count($shippingCarrier) > 0 ) ? $shippingCarrier[0]->id : NULL;
            $order->order_date = $request->date_created;
            $order->payment_method = $request->payment_method_title;
            $order->order_status_id = $orderStatus[0]->id;
            $order->shipping_address1 = $request->shipping['address_1'];
            $order->shipping_address2 = $request->shipping['address_2'];
            $order->city = $request->shipping['city'];
            $order->state = ($request->shipping['state'] != null) ? $request->shipping['state'] : NULL;
            $order->postal = ($request->shipping['postcode'] != null) ? $request->shipping['postcode'] : NULL;
            $order->country = $request->shipping['country'];
            $order->total_weight = $orderWeight;
            $order->total_vol_weight = $orderVolWeight;
            $order->save();

            $orderID = $order->id; // last order id

            if( count($shippingCarrier) == 0 ) // checking if shipmentCarrier is not listed in the database then save data to the api error table
            {
                $apiError = new ApiError;
                $apiError->type = "Shipping Method Mismatch";
                $apiError->ref_id = $orderID;
                $apiError->detail = 'Shipping Method Mismatch';
                $apiError->status = 'error';
                $apiError->save();
            }


            foreach($request->line_items as $item) // getting products data from the wordpress order data
            {
                $product = Products::where('sku', $item['sku'])->get();
                $productID = NULL;
                if( count($product) > 0 )
                {
                    $productID = $product[0]->id;
                } else {
                    $apiError = new ApiError;
                    $apiError->type = "Bad SKU";
                    $apiError->ref_id = $orderID;
                    $apiError->detail = serialize($item);
                    $apiError->status = 'error';
                    $apiError->save();
                }
                
                $orderItem = new OrderItems;
                $orderItem->order_id = $orderID;
                $orderItem->product_id = $productID;
                $orderItem->order_qty = $item['quantity'];
                $orderItem->save();

                if( $productID !== NULL )
                {
                    $dataAvailStock = AvailableStocks::where('product_id', $productID)->where('available_qty','>',$item['quantity'])->get();
                    if( count($dataAvailStock) > 0 )
                    {
                        $availStock = AvailableStocks::find($dataAvailStock[0]->id);
                        $availStock->available_qty = (int)$dataAvailStock[0]->available_qty - (int)$item['quantity'];
                        $availStock->save();

                        $holdStock = new HoldStock;
                        $holdStock->avail_stock_id = $dataAvailStock[0]->id;
                        $holdStock->order_id = $orderID;
                        $holdStock->hold_qty = (int)$item['quantity'];
                        $holdStock->save();

                    } else {
                        $apiError = new ApiError;
                        $apiError->type = "Back Order";
                        $apiError->ref_id = $orderID;
                        $apiError->detail = serialize($item);
                        $apiError->status = 'error';
                        $apiError->save();
                    }

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

}
