<?php
declare(strict_types=1);

namespace app\middleware;

use app\exception\ApiException;
use think\facade\Cache;
use think\Response;

class ApiAuth
{
    /**
     * Token 验证中间件
     */
    public function handle($request, \Closure $next)
    {
        // 获取请求头中的token
        $token = $request->header('Authorization');
        if (empty($token)) {
            return json([
                'code' => 401,
                'message' => '请先登录',
                'data' => []
            ]);
        }

        // 验证token
        $userData = Cache::get('token:' . $token);
        if (empty($userData)) {
            return json([
                'code' => 401,
                'message' => '登录已过期，请重新登录',
                'data' => []
            ]);
        }

        // 将用户信息注入到request中
        $request->userData = $userData;

        return $next($request);
    }
} 