<?php
declare(strict_types=1);

namespace app\middleware;

use app\exception\ApiException;
use think\Response;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\facade\Log;
use Throwable;

class ApiResponse
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        
        // 如果已经是JSON响应，直接返回
        if ($response instanceof Response && $response->getHeader('Content-Type') === 'application/json') {
            return $response;
        }

        // 获取原始数据
        $data = $response->getData();
        
        // 如果数据已经是标准格式，直接返回
        if (is_array($data) && isset($data['code'])) {
            return $response;
        }

        // 格式化响应数据
        $result = [
            'code' => 200,
            'message' => 'success',
            'data' => $data
        ];

        return json($result);
    }
} 