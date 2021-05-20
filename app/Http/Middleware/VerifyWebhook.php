<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyWebhook
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
        try 
        {
            $signature = $request->header('x-wc-webhook-signature');
            $check = base64_encode(hash_hmac('sha256', $request->getContent(), env('WEBHOOK_SECRET'), true));
            if($signature !== $check)
            {
                throw new \Exception('Unauthorized Access');
            }
            return $next($request);
        }
        catch(\Exception $e)
        {
            $error['type']='error';
            $error['msg'] = $e->getMessage();
            return response()->json($error);
        }
    }
}
