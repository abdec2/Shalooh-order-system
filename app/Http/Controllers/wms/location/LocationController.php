<?php

namespace App\Http\Controllers\wms\location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Locations;
use App\Models\WMS\Bins;
use Illuminate\Support\Carbon;

class LocationController extends Controller
{
    function GenerateLocation(Request $request)
    {
        $warehouseStart = $request->warehouseStart;
        $warehouseEnd = $request->warehouseEnd;
        $RowStart = (int)$request->RowStart;
        $RowEnd = (int)$request->RowEnd;
        $BayStart = (int)$request->BayStart;
        $BayEnd = (int)$request->BayEnd;
        $LevelStart = $request->LevelStart;
        $LevelEnd = $request->LevelEnd;
        $option = $request->option;
        $location_category = $request->location_category;
        $bin_init = 1;
        $bin_ending = 1110;

        foreach(range($warehouseStart,$warehouseEnd) as $v)
        {
            
            foreach(range($RowStart, $RowEnd) as $row)
            {
                // echo $v.(($row < 10) ? '0'.$row : $row) . '<br>';
                foreach(range($BayStart, $BayEnd) as $bay){
                    // echo $v.(($row < 10) ? '0'.$row : $row).'-'.(($bay < 10) ? '0'.$bay : $bay).'<br>';
                    foreach(range($LevelStart, $LevelEnd) as $level) {
                        $locationString =  $v.(($row < 10) ? '0'.$row : $row).'-'.(($bay < 10) ? '0'.$bay : $bay).'-'.$level;
                        $loc_Exist = Locations::where('location', $locationString)->get();
                        if(empty($loc_Exist->all()))
                        {
                            $locationData = [
                                "location" => $locationString, 
                                "location_category_id" => $location_category,
                                "total_bins" => $option, 
                                "bins_in_use" => NULL, 
                                "bin_init" => $bin_init,
                                "bin_ending" => $bin_ending,
                            ];
    
                            $location_id = Locations::create($locationData)->id;
                            $this->generateBins($bin_init, $bin_ending,  $location_id, $option, $locationString);
                            $bin_init = $bin_ending + 1;
                            $bin_ending = $bin_ending + 1110;
                        }
                        else {
                            $bin_init =  $loc_Exist[0]->bin_ending + 1;
                            $bin_ending = $loc_Exist[0]->bin_ending + 1110;
                        }
                    }

                }
            }
        }

    } // function ends her 


    function generateBins($bin_init, $bin_ending, $location_id, $option, $locationString)
    {
        $bins = [];
        $address = explode('-', $locationString);
        $addressString = $address[0].'-'.$address[1].$address[2];
        $tagNumber = $bin_init;

        if($option > 110)
        {
            for($i=0; $i < 1000; $i++)
            {
                $binLocation = $addressString.(($i < 10) ? '00'.$i : ($i > 10 && $i < 100) ? '0'.$i : $i);
                // $tagNumber = $tagNumber+1;
                $data = [
                    'bin_location' => $binLocation,
                    'location_id' => $location_id, 
                    'product_id' => NULL,
                    'tag_number' => $tagNumber, 
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];

                array_push($bins, $data);
                $tagNumber++;
            }
        }

        if($option > 10)
        {
            for($i=0; $i < 100; $i++)
            {
                $binLocation = $addressString.(($i < 10) ? '0'.$i : $i);
                // $tagNumber = $tagNumber+1;
                $data = [
                    'bin_location' => $binLocation,
                    'location_id' => $location_id, 
                    'product_id' => NULL,
                    'tag_number' => $tagNumber, 
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];

                array_push($bins, $data);
                $tagNumber++;
            }
        }

        for($i=0; $i < 10; $i++)
        {
            $binLocation = $addressString.$i;
            // $tagNumber = $tagNumber+1;
            $data = [
                'bin_location' => $binLocation,
                'location_id' => $location_id, 
                'product_id' => NULL,
                'tag_number' => $tagNumber, 
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];

            array_push($bins, $data);
            $tagNumber++;
        }

        Bins::insert($bins);

    }
}
