<?php

use think\migration\Seeder;

class UserSeeder extends Seeder
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
        $this->table('user')->insert([
            [
                'username' => 'admin',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'nickname' => '管理员',
                'email' => 'admin@example.com',
                'status' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]
        ])->save();
    }
} 