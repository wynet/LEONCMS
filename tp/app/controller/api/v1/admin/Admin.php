<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Admin as AdminModel;
use app\validate\AdminValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\Request;

class Admin extends BaseController
{
    /**
     * 获取管理员列表
     */
    public function index()
    {
        try {
            // 获取查询参数
            $page = Request::param('page/d', 1);
            $limit = Request::param('limit/d', 10);
            $keyword = Request::param('keyword/s', '');
            $status = Request::param('status/d', '');

            // 构建查询
            $query = AdminModel::with(['role']);

            // 关键词搜索
            if (!empty($keyword)) {
                $query->where('username|nickname', 'like', "%{$keyword}%");
            }

            // 状态筛选
            if ($status !== '') {
                $query->where('status', '=', $status);
            }

            // 分页查询
            $list = $query->order('id', 'desc')
                ->paginate([
                    'list_rows' => $limit,
                    'page' => $page,
                ]);

            return $this->success([
                'list' => $list->items(),
                'total' => $list->total(),
                'page' => $list->currentPage(),
                'limit' => $list->listRows(),
            ]);
            
        } catch (\Exception $e) {
            return $this->error('获取管理员列表失败：' . $e->getMessage());
        }
    }

    /**
     * 创建管理员
     */
    public function save()
    {
        // 验证输入
        try {
            validate(AdminValidate::class)
                ->scene('create')
                ->check($this->request->post());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->post());

        try {
            // 创建管理员
            $admin = AdminModel::create($data);
            
            // 重新获取完整信息
            $admin = AdminModel::with(['role'])
                ->find($admin->id)
                ->append(['status_text']);
            
            return $this->success([
                'admin' => $admin
            ], '创建成功');
        } catch (\Exception $e) {
            return $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新管理员
     */
    public function update($id)
    {
        // 验证输入
        try {
            validate(AdminValidate::class)
                ->scene('update')
                ->check(['id' => $id] + $this->request->put());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找管理员
        $admin = AdminModel::find($id);
        if (!$admin) {
            return $this->error('管理员不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->put());

        // 如果没有提供密码，则移除密码字段
        if (empty($data['password'])) {
            unset($data['password']);
        }

        try {
            // 更新管理员
            $admin->save($data);
            
            // 重新获取完整信息
            $admin = AdminModel::with(['role'])
                ->find($id)
                ->append(['status_text']);
            
            return $this->success([
                'admin' => $admin
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 删除管理员
     * @param int $id 管理员ID
     */
    public function delete($id)
    {
        try {
            // 查找管理员
            $admin = AdminModel::find($id);
            if (!$admin) {
                return $this->error('管理员不存在');
            }

            // 不能删除超级管理员
            if ($admin->role_id === 1) {
                return $this->error('不能删除超级管理员');
            }

            // 删除管理员
            if ($admin->delete()) {
                return $this->success(null, '删除成功');
            } else {
                return $this->error('删除失败');
            }
        } catch (\Exception $e) {
            return $this->error('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 获取管理员信息
     */
    public function info()
    {
        $admin = AdminModel::with(['role'])
            ->find($this->request->user['id'])
            ->append(['status_text']);

        if (!$admin) {
            return $this->error('管理员不存在');
        }

        return $this->success([
            'admin' => $admin
        ]);
    }

    /**
     * 修改密码
     */
    public function password()
    {
        // 验证输入
        try {
            $data = $this->request->post();
            validate(AdminValidate::class)
                ->scene('password')
                ->check($data);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 获取当前管理员
        $admin = AdminModel::find($this->request->adminInfo['id']);
        if (!$admin) {
            return $this->error('管理员不存在');
        }

        // 验证旧密码
        if (!$admin->verifyPassword($data['old_password'])) {
            return $this->error('旧密码错误');
        }

        try {
            // 更新密码
            $admin->save([
                'password' => $data['password']
            ]);
            
            return $this->success(null, '密码修改成功');
        } catch (\Exception $e) {
            return $this->error('密码修改失败：' . $e->getMessage());
        }
    }
} 