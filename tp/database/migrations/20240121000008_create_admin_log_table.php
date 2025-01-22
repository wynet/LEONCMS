<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminLogTable extends Migrator
{
    public function change()
    {
        $this->table('admin_log', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('admin_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '管理员ID'])
            ->addColumn('path', 'string', ['limit' => 100, 'null' => false, 'comment' => '请求路径'])
            ->addColumn('method', 'string', ['limit' => 20, 'null' => false, 'comment' => '请求方法'])
            ->addColumn('ip', 'string', ['limit' => 45, 'null' => false, 'comment' => 'IP地址'])
            ->addColumn('input', 'text', ['null' => true, 'comment' => '请求参数'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addIndex(['admin_id'])
            ->addIndex(['path'])
            ->addIndex(['create_time'])
            ->create();
    }
} 