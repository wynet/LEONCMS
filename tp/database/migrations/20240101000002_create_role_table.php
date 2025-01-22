<?php
declare(strict_types=1);

use think\migration\Migrator;
use think\migration\db\Column;

class CreateRoleTable extends Migrator
{
    public function change()
    {
        $this->table('role')
            ->addColumn('name', 'string', ['limit' => 50, 'comment' => '角色名称'])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true, 'comment' => '角色描述'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态:0禁用,1启用'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['name'], ['unique' => true])
            ->create();

        // 添加默认角色
        $this->insert('role', [
            'name' => '超级管理员',
            'description' => '系统最高权限',
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ]);
    }
} 