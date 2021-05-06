<?php

namespace App\Exceptions;

use Exception;

class NotAnArray extends Exception
{
    function render()
    {
        $error['type']='error';
        $error['msg'] = 'Parameter is not Array';
        return response()->json($error);
    }
}
