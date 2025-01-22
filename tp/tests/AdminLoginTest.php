<?php
declare(strict_types=1);

namespace tests;

use think\facade\Cache;
use app\model\Admin;

class AdminLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 清理缓存
        Cache::clear();
    }

    public function testLoginSuccess()
    {
        $this->post('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => '123456'
        ]);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '登录成功'
            ])
            ->assertResponseJsonStructure([
                'data' => [
                    'token',
                    'admin' => [
                        'id',
                        'username',
                        'nickname',
                        'status',
                        'role_id',
                        'last_login_time',
                        'last_login_ip'
                    ]
                ]
            ]);
    }

    public function testLoginFailedWithWrongPassword()
    {
        $this->post('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => 'wrong_password'
        ]);

        $this->assertResponseStatus(400)
            ->assertResponseJson([
                'code' => 400,
                'message' => '用户名或密码错误'
            ]);
    }

    public function testLoginFailedWithDisabledAccount()
    {
        // 禁用管理员账号
        Admin::where('username', 'admin')->update(['status' => 0]);

        $this->post('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => '123456'
        ]);

        $this->assertResponseStatus(400)
            ->assertResponseJson([
                'code' => 400,
                'message' => '账号已被禁用'
            ]);

        // 恢复管理员账号
        Admin::where('username', 'admin')->update(['status' => 1]);
    }
} 