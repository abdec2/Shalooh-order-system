<?php

namespace App\Http\Controllers\wms\ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Orders;
use App\Models\WMS\OrderStatus;
use App\Models\WMS\OrderAssignedUser;
use App\Models\WMS\pickList;


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
            $statusId = OrderStatus::where(strtoupper('status'), strtoupper('Processing'))->get();

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

            $UserAssignedOrders = OrderAssignedUser::where('user_id', $request->user()->id)->where(strtoupper('status'), strtoupper('pending'))
                                ->with(['order' => function($q){
                                    $q->with('OrderStatus');
                                }])
                                ->whereHas('order', function($q){
                                    $q->whereHas('OrderStatus', function($que){
                                        $que->where(strtoupper('status'), strtoupper('Processing'));
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
            $isExist = OrderAssignedUser::where( strtoupper('status'), strtoupper('pending') )->where( strtoupper('pick_tray'), strtoupper($request->tray) )->where('user_id', $request->user()->id)->get();

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
            $UserAssignedOrders = OrderAssignedUser::where('user_id', $request->user()->id)->where(strtoupper('status'), strtoupper('pending'))
                                ->where('pick_tray', '!=', NULL)
                                ->with(['order' => function($q){
                                    $q->with('OrderStatus');
                                }])
                                ->whereHas('order', function($q){
                                    $q->whereHas('OrderStatus', function($que){
                                        $que->where(strtoupper('status'), strtoupper('Processing'));
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
                OrderAssignedUser::where('id',$value['order_ass_user_id'])->update(['status' => 'picked']);
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


} // class ends here
