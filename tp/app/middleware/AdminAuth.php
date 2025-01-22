<?php
declare(strict_types=1);

namespace app\middleware;

use think\facade\Cache;
use think\Request;

class AdminAuth
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        try {
            // 获取token
            $token = $request->header('Authorization');
            \think\facade\Log::debug('Original Authorization: ' . $token);

            if (empty($token)) {
                return json([
                    'code' => 401,
                    'message' => '请先登录',
                    'data' => null
                ]);
            }

            // 去掉 Bearer 前缀
            $token = str_replace('Bearer ', '', $token);
            $cacheKey = 'token_' . $token;

            // 从缓存获取用户信息
            $admin = Cache::store('file')->get($cacheKey);
            
            // 调试信息
            \think\facade\Log::debug('Auth Token: ' . $token);
            \think\facade\Log::debug('Cache Key: ' . $cacheKey);
            \think\facade\Log::debug('Cache Value: ' . var_export($admin, true));

            if (empty($admin)) {
                return json([
                    'code' => 401,
                    'message' => '登录已过期，请重新登录',
                    'data' => null
                ]);
            }

            // 将用户信息注入到request中
            $request->adminInfo = $admin;

            // 执行请求
            $response = $next($request);

            // 刷新缓存时间
            Cache::store('file')->set($cacheKey, $admin, 7200);

            return $response;
        } catch (\Exception $e) {
            \think\facade\Log::error('Auth Error: ' . $e->getMessage());
            return json([
                'code' => 500,
                'message' => '认证失败：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }
} 