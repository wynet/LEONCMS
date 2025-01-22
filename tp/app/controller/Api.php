<?php
declare(strict_types=1);

namespace app\controller;

use think\Response;
use think\facade\Cache;

class Api extends BaseController
{
    /**
     * 获取信息接口
     * @return Response
     */
    public function info()
    {
        try {
            // 获取当前登录用户信息
            $adminInfo = $this->request->adminInfo;
            
            if (empty($adminInfo)) {
                return json([
                    'code' => 401,
                    'message' => '获取管理员信息失败',
                    'data' => null
                ]);
            }

            // 返回管理员信息
            return json([
                'code' => 200,
                'message' => 'success',
                'data' => [
                    'id' => $adminInfo['id'],
                    'username' => $adminInfo['username'],
                    'nickname' => $adminInfo['nickname'],
                    'login_time' => $adminInfo['login_time']
                ]
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 500,
                'message' => '获取管理员信息异常：' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * 获取管理员信息
     * @return array
     * @throws \Exception
     */
    protected function getAdminInfo(): array
    {
        $token = request()->header('Authorization');
        if (empty($token)) {
            throw new \Exception('未授权访问');
        }

        $cacheKey = 'admin_token:' . $token;
        $adminInfo = Cache::get($cacheKey);
        
        if (empty($adminInfo)) {
            throw new \Exception('登录已过期，请重新登录');
        }

        return $adminInfo;
    }
} 