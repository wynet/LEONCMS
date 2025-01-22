<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminPermissionTable extends Migrator
{
    public function change()
    {
        $this->table('admin_permission', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'comment' => '权限名称'])
            ->addColumn('path', 'string', ['limit' => 100, 'null' => false, 'comment' => '权限路径'])
            ->addColumn('method', 'string', ['limit' => 20, 'null' => false, 'comment' => '请求方法'])
            ->addColumn('parent_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '父级ID'])
            ->addColumn('type', 'string', ['limit' => 20, 'default' => 'menu', 'comment' => '类型：menu菜单 button按钮 api接口'])
            ->addColumn('icon', 'string', ['limit' => 50, 'null' => true, 'comment' => '图标'])
            ->addColumn('component', 'string', ['limit' => 100, 'null' => true, 'comment' => '前端组件'])
            ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['path', 'method'], ['unique' => true])
            ->addIndex(['parent_id'])
            ->addIndex(['sort'])
            ->create();
    }
} 