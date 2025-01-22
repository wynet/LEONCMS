<?php

use think\migration\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * @return void
     */
    public function run(): void
    {
        $this->table('admin_role')->insert([
            [
                'name' => '超级管理员',
                'description' => '系统超级管理员，拥有所有权限',
                'status' => 1,
                'sort' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]
        ])->save();
    }
} 