<?php
declare(strict_types=1);

namespace app\controller\api\v1;

use app\controller\api\BaseController;
use app\model\User;
use app\validate\LoginValidate;
use app\exception\ApiException;
use think\facade\Cache;
use think\facade\Log;
use think\facade\Request;

class Login extends BaseController
{
    /**
     * 用户登录
     */
    public function login()
    {
        try {
            // 获取参数
            $params = $this->request->post();
            Log::info('登录参数：' . json_encode($params, JSON_UNESCAPED_UNICODE));
            
            // 验证参数
            $validate = new LoginValidate();
            if (!$validate->check($params)) {
                Log::warning('登录验证失败：' . $validate->getError());
                throw new ApiException($validate->getError());
            }
            
            // 查找用户
            $user = User::where('username', $params['username'])->find();
            if (!$user) {
                Log::warning('用户不存在：' . $params['username']);
                throw new ApiException('用户不存在');
            }
            
            // 验证密码
            Log::info('密码验证：', [
                'input_password' => $params['password'],
                'stored_hash' => $user->password,
                'verify_result' => password_verify($params['password'], $user->password)
            ]);
            if (!password_verify($params['password'], $user->password)) {
                Log::warning('密码错误：' . $params['username']);
                throw new ApiException('密码错误');
            }
            
            // 更新登录信息
            $user->last_login_time = date('Y-m-d H:i:s');
            $user->last_login_ip = $this->request->ip();
            $user->save();
            
            // 生成token
            $token = md5(uniqid((string)time(), true));
            
            // 缓存用户信息
            Cache::set('token:' . $token, $user->toArray(), 7200); // token有效期2小时
            
            Log::info('用户登录成功：' . $params['username']);
            
            return $this->success([
                'token' => $token,
                'user' => $user
            ], '登录成功');
            
        } catch (\Exception $e) {
            Log::error('登录异常：' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $token = $this->request->header('Authorization');
        if ($token) {
            Cache::delete('token:' . $token);
            Log::info('用户退出登录：' . $token);
        }
        return $this->success([], '退出成功');
    }
} 