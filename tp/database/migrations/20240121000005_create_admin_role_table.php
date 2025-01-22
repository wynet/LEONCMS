<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminRoleTable extends Migrator
{
    public function change()
    {
        $this->table('admin_role', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'comment' => '角色名称'])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true, 'comment' => '角色描述'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['name'], ['unique' => true])
            ->addIndex(['sort'])
            ->create();
    }
} 