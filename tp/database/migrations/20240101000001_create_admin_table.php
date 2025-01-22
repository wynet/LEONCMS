<?php
declare(strict_types=1);

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminTable extends Migrator
{
    public function change()
    {
        $this->table('admin')
            ->addColumn('username', 'string', ['limit' => 50, 'comment' => '用户名'])
            ->addColumn('password', 'string', ['limit' => 255, 'comment' => '密码'])
            ->addColumn('nickname', 'string', ['limit' => 50, 'null' => true, 'comment' => '昵称'])
            ->addColumn('avatar', 'string', ['limit' => 255, 'null' => true, 'comment' => '头像'])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true, 'comment' => '邮箱'])
            ->addColumn('mobile', 'string', ['limit' => 20, 'null' => true, 'comment' => '手机号'])
            ->addColumn('role_id', 'integer', ['signed' => false, 'comment' => '角色ID'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态:0禁用,1启用'])
            ->addColumn('last_login_time', 'datetime', ['null' => true, 'comment' => '最后登录时间'])
            ->addColumn('last_login_ip', 'string', ['limit' => 50, 'null' => true, 'comment' => '最后登录IP'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['username'], ['unique' => true])
            ->create();

        // 添加默认管理员
        $this->insert('admin', [
            'username' => 'admin',
            'password' => password_hash('123456', PASSWORD_DEFAULT),
            'nickname' => '超级管理员',
            'role_id' => 1,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ]);
    }
} 