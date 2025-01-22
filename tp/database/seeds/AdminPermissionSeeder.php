<?php

use think\migration\Seeder;
use think\facade\Db;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run Method.
     */
    public function run(): void
    {
        // 先禁用外键检查
        $this->adapter->getConnection()->exec('SET FOREIGN_KEY_CHECKS=0');
        
        // 清空权限表 - 使用 TRUNCATE 来完全清空表
        $this->adapter->getConnection()->exec('TRUNCATE TABLE admin_permission');
        $this->adapter->getConnection()->exec('TRUNCATE TABLE admin_role_permission');
        
        // 重新启用外键检查
        $this->adapter->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1');
        
        // 系统管理菜单
        $system = $this->createPermission([
            'name' => '系统管理',
            'path' => '/system',
            'method' => 'GET',
            'type' => 'menu',
            'icon' => 'setting',
            'component' => 'Layout',
            'sort' => 100,
        ]);
        
        // 管理员管理
        $admin = $this->createPermission([
            'name' => '管理员管理',
            'path' => '/system/admins',
            'method' => 'GET',
            'type' => 'menu',
            'icon' => 'user',
            'component' => 'system/admin/index',
            'parent_id' => $system['id'],
            'sort' => 101,
        ]);
        
        // 管理员相关权限
        $this->createPermissions([
            [
                'name' => '管理员列表',
                'path' => 'api/v1/admin/admins',
                'method' => 'GET',
                'type' => 'api',
                'parent_id' => $admin['id'],
            ],
            [
                'name' => '创建管理员',
                'path' => 'api/v1/admin/admins',
                'method' => 'POST',
                'type' => 'api',
                'parent_id' => $admin['id'],
            ],
            [
                'name' => '更新管理员',
                'path' => 'api/v1/admin/admins/:id',
                'method' => 'PUT',
                'type' => 'api',
                'parent_id' => $admin['id'],
            ],
            [
                'name' => '删除管理员',
                'path' => 'api/v1/admin/admins/:id',
                'method' => 'DELETE',
                'type' => 'api',
                'parent_id' => $admin['id'],
            ],
        ]);
        
        // 角色管理
        $role = $this->createPermission([
            'name' => '角色管理',
            'path' => '/system/roles',
            'method' => 'GET',
            'type' => 'menu',
            'icon' => 'peoples',
            'component' => 'system/role/index',
            'parent_id' => $system['id'],
            'sort' => 102,
        ]);
        
        // 角色相关权限
        $this->createPermissions([
            [
                'name' => '角色列表',
                'path' => 'api/v1/admin/roles',
                'method' => 'GET',
                'type' => 'api',
                'parent_id' => $role['id'],
            ],
            [
                'name' => '创建角色',
                'path' => 'api/v1/admin/roles',
                'method' => 'POST',
                'type' => 'api',
                'parent_id' => $role['id'],
            ],
            [
                'name' => '更新角色',
                'path' => 'api/v1/admin/roles/:id',
                'method' => 'PUT',
                'type' => 'api',
                'parent_id' => $role['id'],
            ],
            [
                'name' => '删除角色',
                'path' => 'api/v1/admin/roles/:id',
                'method' => 'DELETE',
                'type' => 'api',
                'parent_id' => $role['id'],
            ],
        ]);
        
        // 权限管理
        $permission = $this->createPermission([
            'name' => '权限管理',
            'path' => '/system/permissions',
            'method' => 'GET',
            'type' => 'menu',
            'icon' => 'lock',
            'component' => 'system/permission/index',
            'parent_id' => $system['id'],
            'sort' => 103,
        ]);
        
        // 权限相关权限
        $this->createPermissions([
            [
                'name' => '权限列表',
                'path' => 'api/v1/admin/permissions',
                'method' => 'GET',
                'type' => 'api',
                'parent_id' => $permission['id'],
            ],
            [
                'name' => '创建权限',
                'path' => 'api/v1/admin/permissions',
                'method' => 'POST',
                'type' => 'api',
                'parent_id' => $permission['id'],
            ],
            [
                'name' => '更新权限',
                'path' => 'api/v1/admin/permissions/:id',
                'method' => 'PUT',
                'type' => 'api',
                'parent_id' => $permission['id'],
            ],
            [
                'name' => '删除权限',
                'path' => 'api/v1/admin/permissions/:id',
                'method' => 'DELETE',
                'type' => 'api',
                'parent_id' => $permission['id'],
            ],
        ]);
        
        // 操作日志
        $log = $this->createPermission([
            'name' => '操作日志',
            'path' => '/system/logs',
            'method' => 'GET',
            'type' => 'menu',
            'icon' => 'documentation',
            'component' => 'system/log/index',
            'parent_id' => $system['id'],
            'sort' => 104,
        ]);
        
        // 日志相关权限
        $this->createPermissions([
            [
                'name' => '日志列表',
                'path' => 'api/v1/admin/logs',
                'method' => 'GET',
                'type' => 'api',
                'parent_id' => $log['id'],
            ],
        ]);
        
        // 给超级管理员角色分配所有权限
        $permissions = $this->adapter->fetchAll('SELECT * FROM admin_permission');
        $permissionIds = array_column($permissions, 'id');
        
        if (!empty($permissionIds)) {
            $data = array_map(function($permissionId) {
                return [
                    'role_id' => 1, // 超级管理员角色ID
                    'permission_id' => $permissionId,
                    'create_time' => date('Y-m-d H:i:s')
                ];
            }, $permissionIds);
            
            Db::name('admin_role_permission')->insertAll($data);
        }
    }
    
    /**
     * 创建单个权限
     */
    protected function createPermission($data)
    {
        // 检查权限是否已存在
        $exists = $this->adapter->fetchRow(
            "SELECT * FROM admin_permission WHERE name = '{$data['name']}'"
        );
        
        if ($exists) {
            return $exists; // 如果已存在，直接返回现有记录
        }
        
        // 只包含表中实际存在的字段
        $defaultData = [
            'parent_id' => 0,
            'type' => 'menu',
            'icon' => '',
            'component' => '',
            'sort' => 0,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        // 合并数据，确保只包含有效字段
        $validFields = [
            'name', 'path', 'method', 'parent_id', 'type',
            'icon', 'component', 'sort', 'status',
            'create_time', 'update_time'
        ];
        
        $filteredData = array_intersect_key($data, array_flip($validFields));
        $data = array_merge($defaultData, $filteredData);
        
        // 使用 insert() 方法插入数据
        $this->table('admin_permission')->insert($data)->save();
        
        // 获取最后插入的ID
        $id = $this->adapter->getConnection()->lastInsertId();
        
        // 返回完整的数据数组
        return array_merge($data, ['id' => $id]);
    }
    
    /**
     * 批量创建权限
     */
    protected function createPermissions($dataList)
    {
        foreach ($dataList as $data) {
            $this->createPermission($data);
        }
    }
} 