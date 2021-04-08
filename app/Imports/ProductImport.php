<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\WMS\Products;
use App\Models\WMS\Bins;
use App\Models\WMS\Inventory;

class ProductImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            echo '<pre>';
            print_r($row);
            echo '</pre>';

            // check sku exist or not
            $product = Products::where('sku', $row['sku'])->get();
            // check bin exist or not
            $bin = Bins::where('bin_location', $row['bin_location'])->get();
            $productID = NULL;
            $parentSkuId = NULL;

            // check for parent sku
            if($row['parent_sku'] !== null)
            {
                $parentSKU = Products::where('sku', $row['parent_sku'])->get(); // check parent sku in db
                if( count($parentSKU) > 0 ) // check if its exists or not 
                {
                    // if exist take its id
                    $parentSkuId = $parentSKU[0]->id;
                } else {
                    // else create parent sku and take its id
                    $parentSkuData = [
                        "label" => $row['label'],
                        "sku" => $row['parent_sku'],
                        "parent" => NULL, 
                        "image_path" => NULL
                    ];
                    $parentSkuId = Products::create($parentSkuData)->id;
                }

            }

            if(count($product) > 0)
            {
                // product exist, Update Product
                $updateProduct = Products::find($product[0]->id);
                $updateProduct->label = $row['label'];
                $updateProduct->sku = $row['sku'];
                $updateProduct->parent = $row['parent_sku'];
                $updateProduct->image_path = $row['image_path'];
                $updateProduct->save();
                $productID = $updateProduct->id;
            }
            else 
            {
                // product not exist, Create Product 
                $productData = [
                    "label" => $row['label'],
                    "sku" => $row['sku'],
                    "parent" => $row['parent_sku'], 
                    "image_path" => $row['image_path']
                ];
                $productID = Products::create($productData)->id;
            }

            if(count($bin) > 0) {
                if($bin[0]->product_id == $productID || $bin[0]->product_id == null)
                {
                    $updateBinRecord = Bins::find($bin[0]->id);
                    $updateBinRecord->product_id = $productID;
                    $updateBinRecord->save();
                    
                    $inventoryRecord = Inventory::where('bin_id', $bin[0]->id)->get();
                    if(count($inventoryRecord) > 0 ) {
                        $updateInventory = Inventory::find($inventoryRecord[0]->id);
                        $updateInventory->quantity = (int)$inventoryRecord[0]->quantity + (int)$row['qty'];
                        $updateInventory->save();
                    } else {

                        $inventoryInsert = new Inventory;
                        $inventoryInsert->bin_id = $bin[0]->id;
                        $inventoryInsert->quantity = $row['qty'];
                        $inventoryInsert->save();
                    }

                }
            }

        }

    }
}
