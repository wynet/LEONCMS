<?php
declare(strict_types=1);

namespace tests;

use app\model\Admin;
use think\facade\Cache;

class AdminTest extends TestCase
{
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        // 登录获取token
        $this->post('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => '123456'
        ]);
        $data = $this->getResponseData();
        $this->token = $data['data']['token'] ?? '';
    }

    public function testAdminList()
    {
        $this->withHeader('Authorization', $this->token)
            ->get('/api/v1/admin/admins');

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => 'success'
            ])
            ->assertResponseJsonStructure([
                'data' => [
                    'list' => [
                        '*' => [
                            'id',
                            'username',
                            'nickname',
                            'status',
                            'role_id',
                            'role' => [
                                'id',
                                'name'
                            ]
                        ]
                    ],
                    'total',
                    'last_page',
                    'current_page'
                ]
            ]);
    }

    public function testCreateAdmin()
    {
        $this->withHeader('Authorization', $this->token)
            ->post('/api/v1/admin/admins', [
                'username' => 'test_admin',
                'password' => '123456',
                'nickname' => '测试账号',
                'status' => 1,
                'role_id' => 2
            ]);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '创建成功'
            ]);

        // 验证数据库是否创建成功
        $this->assertDatabaseHas('admin', [
            'username' => 'test_admin',
            'nickname' => '测试账号'
        ]);
    }

    public function testUpdateAdmin()
    {
        // 先确保测试账号存在
        $admin = Admin::where('username', 'test_admin')->find();
        if (!$admin) {
            $this->testCreateAdmin();
            $admin = Admin::where('username', 'test_admin')->find();
        }

        $this->withHeader('Authorization', $this->token)
            ->put('/api/v1/admin/admins/' . $admin->id, [
                'nickname' => '测试账号已更新',
                'status' => 1,
                'role_id' => 2
            ]);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '更新成功'
            ]);

        // 验证数据库是否更新成功
        $this->assertDatabaseHas('admin', [
            'id' => $admin->id,
            'nickname' => '测试账号已更新'
        ]);
    }

    public function testDeleteAdmin()
    {
        // 先确保测试账号存在
        $admin = Admin::where('username', 'test_admin')->find();
        if (!$admin) {
            $this->testCreateAdmin();
            $admin = Admin::where('username', 'test_admin')->find();
        }

        $this->withHeader('Authorization', $this->token)
            ->delete('/api/v1/admin/admins/' . $admin->id);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '删除成功'
            ]);

        // 验证数据库是否删除成功
        $this->assertDatabaseMissing('admin', [
            'id' => $admin->id
        ]);
    }

    public function testCannotDeleteSuperAdmin()
    {
        $this->withHeader('Authorization', $this->token)
            ->delete('/api/v1/admin/admins/1');

        $this->assertResponseStatus(400)
            ->assertResponseJson([
                'code' => 400,
                'message' => '不能删除超级管理员'
            ]);
    }
} 