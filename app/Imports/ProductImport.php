<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\WMS\Products;
use App\Models\WMS\Bins;
use App\Models\WMS\Inventory;
use App\Models\WMS\AvailableStocks;

use Illuminate\Support\Facades\Log;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        ini_set('max_execution_time', 3000); 
        foreach ($rows as $row) 
        {
            // create log   adding sku 
            Log::channel('addProduct')->info('Adding SKU : '.$row['sku']);
            // check sku exist or not
            $product = Products::where('sku', $row['sku'])->get();
            // check bin exist or not
            $bin = Bins::where('bin_location', $row['bin_location'])->with(['Locations'=>function($q){
                $q->with('LocationCategories');
            }])->get();
            $productID = NULL;
            $parentSkuId = NULL;
            $locationCatagory = NULL;
            
            // check for parent sku
            if($row['parent_sku'] !== null)
            {
                Log::channel('addProduct')->info('Checking Parent SKU : '.$row['parent_sku']);
                $parentSKU = Products::where('sku', $row['parent_sku'])->get(); // check parent sku in db

                if( count($parentSKU) > 0 ) // check if its exists or not 
                {
                    // if exist take its id
                    $parentSkuId = $parentSKU[0]->id;
                    Log::channel('addProduct')->info('Parent SKU found, : '.$row['parent_sku']);
                } else {
                    // else create parent sku and take its id
                    Log::channel('addProduct')->info('Parent SKU not found, Creating Parent SKU');
                    $parentSkuData = [
                        "label" => $row['label'],
                        "sku" => $row['parent_sku'],
                        "parent" => NULL, 
                        "image_path" => asset('/img/placeholder.jpg'),
                        "is_parent" => 'Y'
                    ];
                    $parentSkuId = Products::create($parentSkuData)->id;
                    Log::channel('addProduct')->info($row['parent_sku'].' SKU Created');
                }
                
            }
            
            // dd($parentSkuId);
            if(count($product) > 0)
            {
                Log::channel('addProduct')->info($row['sku']. ' already exist. now updating..');
                // product exist, Update Product
                $updateProduct = Products::find($product[0]->id);
                $updateProduct->label = $row['label'];
                $updateProduct->sku = $row['sku'];
                $updateProduct->parent = $parentSkuId;
                $updateProduct->image_path = ($row['image_path'] !== null) ? $row['image_path'] : asset('/img/placeholder.jpg');
                $updateProduct->save();
                $productID = $updateProduct->id;

                Log::channel('addProduct')->info($row['sku'].' Updated.');
            }
            else 
            {
                // product not exist, Create Product 
                Log::channel('addProduct')->info($row['sku']. ' does not exist. now creating..');
                $productData = [
                    "label" => $row['label'],
                    "sku" => $row['sku'],
                    "parent" => $parentSkuId, 
                    "image_path" => ($row['image_path'] !== null) ? $row['image_path'] : asset('/img/placeholder.jpg'),
                ];
                $productID = Products::create($productData)->id;
                Log::channel('addProduct')->info($row['sku']. ' created.');
            }

            if(count($bin) > 0) {
                if($bin[0]->product_id == $productID || $bin[0]->product_id == null)
                {
                    $locationCatagory = $bin[0]->Locations->LocationCategories->category;

                    Log::channel('addProduct')->info('Assigning bin location '.$bin[0]->bin_location.' to SKU '. $row['sku']);
                    $updateBinRecord = Bins::find($bin[0]->id);
                    $updateBinRecord->product_id = $productID;
                    $updateBinRecord->save();
                    
                    $inventoryRecord = Inventory::where('bin_id', $bin[0]->id)->get();
                    if(count($inventoryRecord) > 0 ) {
                        Log::channel('addProduct')->info('adding stocks to bin '.$bin[0]->bin_location.' for SKU '. $row['sku']);
                        $updateInventory = Inventory::find($inventoryRecord[0]->id);
                        $updateInventory->quantity = (int)$inventoryRecord[0]->quantity + (int)$row['qty'];
                        $updateInventory->save();
                    } else {
                        Log::channel('addProduct')->info('adding stocks to bin '.$bin[0]->bin_location.' for SKU '. $row['sku']);
                        $inventoryInsert = new Inventory;
                        $inventoryInsert->bin_id = $bin[0]->id;
                        $inventoryInsert->quantity = $row['qty'];
                        $inventoryInsert->save();
                    }

                    if(strtoupper($locationCatagory) !== strtoupper('Hurt'))
                    {
                        $avail_stock = AvailableStocks::where('product_id', $productID)->get();
                        if(count($avail_stock) > 0) 
                        {
                            $avstock = AvailableStocks::find($avail_stock[0]->id);
                            $avstock->available_qty = (int)$avail_stock[0]->available_qty + (int)$row['qty'];
                            $avstock->save();
                        }
                        else {
                            $a_stock = new AvailableStocks;
                            $a_stock->product_id = $productID;
                            $a_stock->available_qty = (int)$row['qty'];
                            $a_stock->save();
                        }
                    }

                } else {
                    Log::channel('addProduct')->info('Assigning bin location '.$bin[0]->bin_location.' to SKU '. $row['sku']);
                    Log::channel('addProduct')->info('bin does not belongs to this SKU');
                }
            }

            



        }

        ini_set('max_execution_time', 300); 

    }
}
