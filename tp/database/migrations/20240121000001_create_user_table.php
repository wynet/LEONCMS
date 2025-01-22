<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateUserTable extends Migrator
{
    public function change()
    {
        $this->table('user', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('username', 'string', ['limit' => 50, 'null' => false, 'comment' => '用户名'])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false, 'comment' => '密码'])
            ->addColumn('nickname', 'string', ['limit' => 50, 'null' => true, 'comment' => '昵称'])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
            ->addColumn('mobile', 'string', ['limit' => 11, 'null' => true, 'comment' => '手机号'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('last_login_time', 'datetime', ['null' => true, 'comment' => '最后登录时间'])
            ->addColumn('last_login_ip', 'string', ['limit' => 45, 'null' => true, 'comment' => '最后登录IP'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['mobile'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
} 