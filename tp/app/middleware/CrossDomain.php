<?php
declare(strict_types=1);

namespace app\middleware;

use think\Response;

class CrossDomain
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        
        if ($response instanceof Response) {
            $response->header([
                'Access-Control-Allow-Origin'      => '*',
                'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
                'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age'           => '1800',
            ]);
        }

        if ($request->method(true) == 'OPTIONS') {
            return response()->code(204);
        }

        return $response;
    }
} 