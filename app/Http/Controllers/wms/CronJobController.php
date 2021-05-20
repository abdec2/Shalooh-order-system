<?php

namespace App\Http\Controllers\wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\CronJobModel;
use App\Models\WMS\Products;
use App\Classes\livesite\WoocommerceClass;

class CronJobController extends Controller
{
    function StockUpdateTrigger(Request $request, $sk)
    {
        try
        {
            $exist = CronJobModel::where(strtoupper('status'), strtoupper('process'))->where(strtoupper('cron_type'), strtoupper('Update Cron'))->get();

            if( count($exist) > 0 )
            {
                throw new \Exception('Process Already Running');
            }

            $cronjob = new CronJobModel;
            $cronjob->cron_type = 'Update Cron';
            $cronjob->status = 'process';
            $cronjob->save();

            $success['type']='success';
            $success['msg'] = 'Cron Job triggerred';
            return response()->json($success);

        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    } // function ends here

    function StockUpdateProcess(Request $request, $sk)
    {
        try
        {
            // get cron details
            $cron = CronJobModel::where(strtoupper('status'), strtoupper('process'))->where(strtoupper('cron_type'), strtoupper('Update Cron'))->get();
            // check if there is any running job
            if(count($cron) == 0)
            {
                throw new \Exception('No job is processing');
            }
            // check last product id. if its null then start from first product else start after the last product id. we will fetch 20 products and update stock
            $lastPID = $cron[0]['last_pid'];
            $lastSku = $cron[0]['sku'];
            if( $lastPID !== null )
            {
                $products = Products::where('id','>', $lastPID)->where('is_parent', 'N')->with('AvailableStock')->limit(20)->get();
            } else {
                $products = Products::where('is_parent', 'N')->with('AvailableStock')->limit(20)->get();
            }
            if( count($products) > 0 )
            {
                foreach($products as $product)
                {
                    $data = [
                        'sku' => $product->sku
                    ];
                    $wpProduct = json_decode(WoocommerceClass::getProduct(env('LIVE_SITE').'/wp-json/wc/v3/products', $data), true);
                    
                    if( count($wpProduct) > 0 )
                    {
                        if(strtoupper($wpProduct[0]['type']) == strtoupper('simple'))
                        {
                            WoocommerceClass::stockUpdate(env('LIVE_SITE').'/wp-json/wc/v3/products/'.$wpProduct[0]["id"], '{ "stock_quantity" : '.$product->AvailableStock['available_qty'].'}');
                        }
                        else {
                            WoocommerceClass::stockUpdate(env('LIVE_SITE').'/wp-json/wc/v3/products/'.$wpProduct[0]["parent_id"].'/variations/'.$wpProduct[0]["id"], '{ "stock_quantity" : '.$product->AvailableStock['available_qty'].'}');
                        }
                    }
                }

                $cronUpdate = CronJobModel::find($cron[0]->id);
                $cronUpdate->last_pid = $products[count($products) - 1]->id;
                $cronUpdate->sku = $products[count($products) - 1]->sku;
                $cronUpdate->save();

            } else {
                $cronStatusUpdate = CronJobModel::find($cron[0]->id);
                $cronStatusUpdate->status = 'finish';
                $cronStatusUpdate->save();
            }

        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }

    } //function ends here
}
