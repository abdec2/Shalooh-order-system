<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCron
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $RequestSk = $request->route()->parameter('sk');
        $sk = env('CRON_JOB_SK');

        if($RequestSk !== $sk)
        {
            $error['type']='error';
            $error['msg'] = 'Request Not Authorized';
            return response()->json($error);            
        }

        return $next($request);
    }
}
