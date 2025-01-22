<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminRolePermissionTable extends Migrator
{
    public function change()
    {
        $this->table('admin_role_permission', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('role_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '角色ID'])
            ->addColumn('permission_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '权限ID'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addIndex(['role_id', 'permission_id'], ['unique' => true])
            ->create();
    }
} 