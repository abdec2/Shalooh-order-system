<?php

namespace App\Http\Controllers\wms\products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;

class ProductsController extends Controller
{   

    function add_products(Request $request)
    {
        $upload = $request->file('csvUpload');
        if($upload->getClientOriginalExtension() == 'csv' || $upload->getClientOriginalExtension() == 'xlsx' || $upload->getClientOriginalExtension() == 'xls')
        {
            Excel::import(new ProductImport, $upload);
        } else
        {
            $result['type'] = 'error';
            $result['msg'] = 'Invalid File Type. use excel or csv files';
            return view('wms/addProducts', $result);
        }

    } // function ends here
}
