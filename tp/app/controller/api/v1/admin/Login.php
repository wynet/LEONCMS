<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Admin as AdminModel;
use app\validate\LoginValidate;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Session;

class Login extends BaseController
{
    /**
     * 获取登录验证码
     */
    public function getLoginCode()
    {
        try {
            // 验证输入
            $data = $this->request->get();
            validate(LoginValidate::class)
                ->scene('getCode')
                ->check($data);

            // 检查用户是否存在
            $admin = AdminModel::where('username', $data['username'])->find();
            if (!$admin) {
                return $this->error('用户不存在');
            }

            // 生成6位随机验证码
            $code = sprintf('%06d', mt_rand(0, 999999));
            
            // 缓存验证码，有效期5分钟
            Cache::set('login_code_' . $data['username'], $code, 300);

            // TODO: 在实际项目中，这里应该发送验证码到用户邮箱或手机
            // 这里为了测试，直接返回验证码
            return $this->success([
                'code' => $code
            ], '验证码已发送');
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        } catch (\Exception $e) {
            return $this->error('获取验证码失败：' . $e->getMessage());
        }
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        try {
            // 验证输入
            $data = $this->request->post();
            validate(LoginValidate::class)
                ->scene('login')
                ->check($data);

            // 验证验证码
            if (!$this->checkVerifyCode($data['username'], $data['code'])) {
                return $this->error('验证码错误或已过期');
            }

            // 查找管理员
            $admin = AdminModel::where('username', $data['username'])
                ->withJoin('role', 'LEFT')
                ->find();

            if (!$admin) {
                return $this->error('用户名或密码错误');
            }

            // 验证密码
            if (!$admin->verifyPassword($data['password'])) {
                return $this->error('用户名或密码错误');
            }

            // 验证状态
            if ($admin->status !== 1) {
                return $this->error('账号已被禁用');
            }

            // 更新登录信息
            $admin->save([
                'last_login_time' => date('Y-m-d H:i:s'),
                'last_login_ip' => $this->request->ip()
            ]);

            // 生成 token
            $token = md5(uniqid((string)mt_rand(), true));
            $bearerToken = 'Bearer ' . $token;

            // 缓存用户信息
            $adminInfo = $admin->toArray();
            $cacheKey = 'token_' . $token;

            // 先清除可能存在的旧缓存
            Cache::clear();

            // 设置缓存，有效期2小时
            $result = Cache::store('file')->set($cacheKey, $adminInfo, 7200);

            // 调试信息
            \think\facade\Log::debug('Login Token: ' . $token);
            \think\facade\Log::debug('Bearer Token: ' . $bearerToken);
            \think\facade\Log::debug('Cache Key: ' . $cacheKey);
            \think\facade\Log::debug('Cache Set Result: ' . var_export($result, true));
            \think\facade\Log::debug('Cache Value: ' . var_export(Cache::store('file')->get($cacheKey), true));
            \think\facade\Log::debug('Cache Path: ' . runtime_path() . 'cache/');
            \think\facade\Log::debug('Cache Files: ' . var_export(glob(runtime_path() . 'cache/*'), true));

            if (!$result) {
                return $this->error('登录失败：缓存设置失败');
            }

            return $this->success([
                'token' => $bearerToken,
                'admin' => $adminInfo
            ], '登录成功');

        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        } catch (\Exception $e) {
            return $this->error('登录失败：' . $e->getMessage());
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        try {
            // 获取 token
            $token = $this->request->header('Authorization');
            if ($token) {
                // 清除缓存
                $token = str_replace('Bearer ', '', $token);
                Cache::delete('admin_token_' . $token);
                Cache::tag('admin_token')->clear();
            }
            
            return $this->success(null, '退出成功');
        } catch (\Exception $e) {
            return $this->error('退出失败：' . $e->getMessage());
        }
    }

    /**
     * 验证验证码
     */
    protected function checkVerifyCode(string $username, string $code): bool
    {
        $cacheCode = Cache::get('login_code_' . $username);
        if (!$cacheCode || $cacheCode !== $code) {
            return false;
        }
        
        // 验证成功后删除缓存
        Cache::delete('login_code_' . $username);
        return true;
    }
} 