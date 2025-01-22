<?php
declare(strict_types=1);

namespace app\controller\api\v1;

use app\BaseController;
use app\model\User as UserModel;
use app\validate\UserValidate;
use think\exception\ValidateException;
use think\facade\Log;
use think\facade\Db;

class User extends BaseController
{
    /**
     * 获取用户信息
     */
    public function info()
    {
        $user = UserModel::find($this->request->user['id'])
            ->append(['status_text']);

        if (!$user) {
            return $this->error('用户不存在');
        }

        return $this->success([
            'user' => $user
        ]);
    }

    /**
     * 修改密码
     */
    public function password()
    {
        // 验证输入
        try {
            validate(UserValidate::class)
                ->scene('password')
                ->check($this->request->post());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 获取当前用户
        $user = UserModel::find($this->request->user['id']);
        if (!$user) {
            return $this->error('用户不存在');
        }

        // 验证旧密码
        if (!$user->verifyPassword($this->request->post('old_password'))) {
            return $this->error('旧密码错误');
        }

        try {
            // 更新密码
            $user->save([
                'password' => $this->request->post('password')
            ]);
            
            return $this->success(null, '密码修改成功');
        } catch (\Exception $e) {
            return $this->error('密码修改失败：' . $e->getMessage());
        }
    }

    /**
     * 更新用户信息
     */
    public function update()
    {
        // 验证输入
        try {
            validate(UserValidate::class)
                ->scene('update')
                ->check($this->request->post());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 获取当前用户
        $user = UserModel::find($this->request->user['id']);
        if (!$user) {
            return $this->error('用户不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->post());

        try {
            // 更新用户信息
            $user->save($data);
            
            // 重新获取完整信息
            $user = UserModel::find($user->id)
                ->append(['status_text']);
            
            return $this->success([
                'user' => $user
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }
} 