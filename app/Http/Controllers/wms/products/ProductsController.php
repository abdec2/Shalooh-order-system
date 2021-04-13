<?php

namespace App\Http\Controllers\wms\products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Imports\ProductImportOverrideStocks;


class ProductsController extends Controller
{   

    function add_products(Request $request)
    {
        
        $upload = $request->file('csvUpload');
        if($upload->getClientOriginalExtension() == 'csv' || $upload->getClientOriginalExtension() == 'xlsx' || $upload->getClientOriginalExtension() == 'xls')
        {
            
            if($request->override)
            {
                Excel::import(new ProductImportOverrideStocks, $upload);
            } else {
            }
            return redirect('/wms/products/list')->with('status', 'Products Added Successfully');
        } else
        {
            $result['type'] = 'error';
            $result['msg'] = 'Invalid File Type. use excel or csv files';
            return view('wms/addProducts', $result);
        }

    } // function ends here
}
