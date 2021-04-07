<?php

namespace App\Http\Controllers\wms\location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WMS\Locations;
use App\Models\WMS\Bins;


class BinController extends Controller
{
    function generateBins()
    {
        
        $locations = Locations::all();
        foreach($locations->all() as $key => $location)
        {
            $location_id = $location->id;
            $address = $location->location;
            $tag = $this->generateLocationTag($address);
            $bins = [];
            for($i = 0; $i < 100; $i++)
            {
                $binLocation = $address.(($i < 10) ? '0'.$i : $i);
                $tagNumber = $tag.(($i < 10) ? '0'.$i : $i);
                $data = [
                    'id' => NULL,
                    'bin_location' => $binLocation,
                    'location_id' => $location_id, 
                    'product_id' => NULL,
                    'tag_number' => (int)$tagNumber
                ];

                array_push($bins, $data);
                
            }

            Bins::insert($bins);
        }
        
    }

    function generateLocationTag($location)
    {
        $Mapping_values = [
            'A' => 0,
            'B' => 1,
            'C' => 2,
            'D' => 3,
            'E' => 4,
            'F' => 5,
            'G' => 6,
            'H' => 7,
            'I' => 8,
            'J' => 9,
            'K' => 10,
            'L' => 11,
            'M' => 12,
            'N' => 13,
            'O' => 14,
            'P' => 15,
            'Q' => 16,
            'R' => 17,
            'S' => 18,
            'T' => 19,
            'U' => 20,
            'V' => 21,
            'W' => 22,
            'X' => 23,
            'Y' => 24,
            'Z' => 25

        ];

        $result = '';
        $location = str_split($location);
        foreach($location as $char)
        {
            if($char !== '-')
            {
                if(array_key_exists($char, $Mapping_values))
                {
                    $result .= ($Mapping_values[$char] < 10) ? '0'.$Mapping_values[$char] : $Mapping_values[$char];
                } else 
                {
                    $result .= $char;
                }
            }

        }
        return $result;

    }
}
