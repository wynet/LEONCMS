<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Role as RoleModel;
use app\validate\RoleValidate;
use think\exception\ValidateException;

class Role extends BaseController
{
    /**
     * 角色列表
     */
    public function index()
    {
        // 验证输入参数
        try {
            validate(RoleValidate::class)
                ->scene('index')
                ->check($this->request->only([
                    'page', 'pageSize', 'keyword', 'status'
                ]));
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 设置默认值
        $page = (int)($this->request->param('page', 1));
        $pageSize = (int)($this->request->param('pageSize', 15));

        // 构建查询
        $query = RoleModel::with(['permissions']);

        // 关键词搜索
        if ($keyword = $this->request->param('keyword')) {
            $query->where('name|description', 'like', "%{$keyword}%");
        }

        // 状态筛选
        if (($status = $this->request->param('status')) !== null) {
            $query->where('status', $status);
        }

        // 获取数据
        $total = $query->count();
        $list = $query->page($page, $pageSize)
            ->order('create_time', 'desc')
            ->select()
            ->append(['status_text'])
            ->toArray();

        return $this->success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ]);
    }

    /**
     * 创建角色
     */
    public function save()
    {
        // 验证输入
        try {
            validate(RoleValidate::class)
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
            // 创建角色
            $role = RoleModel::create($data);
            
            // 处理权限关联
            if (!empty($data['permission_ids'])) {
                $role->permissions()->attach($data['permission_ids']);
            }
            
            // 重新获取完整信息
            $role = RoleModel::with(['permissions'])
                ->find($role->id)
                ->append(['status_text']);
            
            return $this->success([
                'role' => $role
            ], '创建成功');
        } catch (\Exception $e) {
            return $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新角色
     */
    public function update($id)
    {
        // 验证输入
        try {
            validate(RoleValidate::class)
                ->scene('update')
                ->check(['id' => $id] + $this->request->put());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找角色
        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->put());

        try {
            // 更新角色基本信息
            $role->save($data);
            
            // 更新权限关联
            if (isset($data['permission_ids'])) {
                $role->permissions()->detach();
                if (!empty($data['permission_ids'])) {
                    $role->permissions()->attach($data['permission_ids']);
                }
            }
            
            // 重新获取完整信息
            $role = RoleModel::with(['permissions'])
                ->find($id)
                ->append(['status_text']);
            
            return $this->success([
                'role' => $role
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 删除角色
     */
    public function delete($id)
    {
        // 验证输入
        try {
            validate(RoleValidate::class)
                ->scene('delete')
                ->check(['id' => $id]);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找角色
        $role = RoleModel::find($id);
        if (!$role) {
            return $this->error('角色不存在');
        }

        // 不能删除超级管理员角色
        if ($id === 1) {
            return $this->error('不能删除超级管理员角色');
        }

        // 检查是否有管理员使用此角色
        $hasAdmin = \app\model\Admin::where('role_id', $id)->find();
        if ($hasAdmin) {
            return $this->error('该角色下还有管理员，不能删除');
        }

        try {
            // 删除角色及其权限关联
            $role->permissions()->detach();
            $role->delete();
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error('删除失败：' . $e->getMessage());
        }
    }
} 