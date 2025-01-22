<?php
declare(strict_types=1);

namespace app\middleware;

use think\facade\Cache;
use think\Request;

class AdminPermission
{
    public function handle($request, \Closure $next)
    {
        // 获取用户信息
        $admin = $request->adminInfo;
        if (empty($admin)) {
            return json([
                'code' => 401,
                'message' => '请先登录',
                'data' => null
            ]);
        }

        // 超级管理员不需要验证权限
        if ($admin['role_id'] === 1) {
            return $next($request);
        }

        // TODO: 验证权限

        return $next($request);
    }
} 