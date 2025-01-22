<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Permission as PermissionModel;
use app\validate\PermissionValidate;
use think\exception\ValidateException;

class Permission extends BaseController
{
    /**
     * 权限列表
     */
    public function index()
    {
        // 验证输入参数
        try {
            validate(PermissionValidate::class)
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
        $query = PermissionModel::order('id', 'asc');

        // 关键词搜索
        if ($keyword = $this->request->param('keyword')) {
            $query->where('name|path|description', 'like', "%{$keyword}%");
        }

        // 状态筛选
        if (($status = $this->request->param('status')) !== null) {
            $query->where('status', $status);
        }

        // 获取数据
        $total = $query->count();
        $list = $query->page($page, $pageSize)
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
     * 创建权限
     */
    public function save()
    {
        // 验证输入
        try {
            validate(PermissionValidate::class)
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
            // 创建权限
            $permission = PermissionModel::create($data);
            
            // 重新获取完整信息
            $permission = PermissionModel::find($permission->id)
                ->append(['status_text']);
            
            return $this->success([
                'permission' => $permission
            ], '创建成功');
        } catch (\Exception $e) {
            return $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新权限
     */
    public function update($id)
    {
        // 验证输入
        try {
            validate(PermissionValidate::class)
                ->scene('update')
                ->check(['id' => $id] + $this->request->put());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找权限
        $permission = PermissionModel::find($id);
        if (!$permission) {
            return $this->error('权限不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->put());

        try {
            // 更新权限
            $permission->save($data);
            
            // 重新获取完整信息
            $permission = PermissionModel::find($id)
                ->append(['status_text']);
            
            return $this->success([
                'permission' => $permission
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 删除权限
     */
    public function delete($id)
    {
        // 验证输入
        try {
            validate(PermissionValidate::class)
                ->scene('delete')
                ->check(['id' => $id]);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找权限
        $permission = PermissionModel::find($id);
        if (!$permission) {
            return $this->error('权限不存在');
        }

        // 检查是否有角色使用此权限
        $hasRole = $permission->roles()->find();
        if ($hasRole) {
            return $this->error('该权限已被角色使用，不能删除');
        }

        try {
            // 删除权限
            $permission->delete();
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error('删除失败：' . $e->getMessage());
        }
    }
} 