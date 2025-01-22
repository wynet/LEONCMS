<?php
declare(strict_types=1);

namespace app\middleware;

use think\Response;

class CheckAuth
{
    public function handle($request, \Closure $next)
    {
        // 设置响应头
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            return $next($request);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }
} 