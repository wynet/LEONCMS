<?php

use think\migration\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * @return void
     */
    public function run(): void
    {
        $this->table('admin')->insert([
            [
                'username' => 'admin',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'nickname' => '超级管理员',
                'status' => 1,
                'role_id' => 1, // 超级管理员角色
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]
        ])->save();
    }
} 