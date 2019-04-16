<?php

namespace App\Http\Middleware;

use Closure;

class CompressHTML
{

    public function handle ($request, Closure $next)
    {

        if (!$request->is('user/payment/receipt/*'))
        {
            $response = $next($request);
            $response->headers->set('X-Route-final-Time', round((microtime(true) - LARAVEL_START) * 1000, 3).' ms');
            if ($request->isMethod('get'))
            {
                $response->setContent(compressContent($response->getContent()));
            }
            return $response;
        }
        else
        {
            return $next($request);
        }
    }

}
