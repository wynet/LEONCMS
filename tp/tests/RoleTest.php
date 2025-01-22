<?php
declare(strict_types=1);

namespace tests;

use app\model\AdminRole;
use think\facade\Cache;

class RoleTest extends TestCase
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

    public function testRoleList()
    {
        $this->withHeader('Authorization', $this->token)
            ->get('/api/v1/admin/roles');

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
                            'name',
                            'description',
                            'status',
                            'sort',
                            'permissions'
                        ]
                    ],
                    'total',
                    'last_page',
                    'current_page'
                ]
            ]);
    }

    public function testCreateRole()
    {
        $this->withHeader('Authorization', $this->token)
            ->post('/api/v1/admin/roles', [
                'name' => '测试角色',
                'description' => '测试角色描述',
                'status' => 1,
                'sort' => 0,
                'permission_ids' => [1, 2, 3]
            ]);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '创建成功'
            ]);

        // 验证数据库是否创建成功
        $this->assertDatabaseHas('admin_role', [
            'name' => '测试角色',
            'description' => '测试角色描述'
        ]);
    }

    public function testUpdateRole()
    {
        // 先确保测试角色存在
        $role = AdminRole::where('name', '测试角色')->find();
        if (!$role) {
            $this->testCreateRole();
            $role = AdminRole::where('name', '测试角色')->find();
        }

        $this->withHeader('Authorization', $this->token)
            ->put('/api/v1/admin/roles/' . $role->id, [
                'name' => '测试角色已更新',
                'description' => '测试角色描述已更新',
                'status' => 1,
                'sort' => 0,
                'permission_ids' => [1, 2, 3, 4]
            ]);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '更新成功'
            ]);

        // 验证数据库是否更新成功
        $this->assertDatabaseHas('admin_role', [
            'id' => $role->id,
            'name' => '测试角色已更新',
            'description' => '测试角色描述已更新'
        ]);
    }

    public function testDeleteRole()
    {
        // 先确保测试角色存在
        $role = AdminRole::where('name', '测试角色已更新')->find();
        if (!$role) {
            $this->testUpdateRole();
            $role = AdminRole::where('name', '测试角色已更新')->find();
        }

        $this->withHeader('Authorization', $this->token)
            ->delete('/api/v1/admin/roles/' . $role->id);

        $this->assertResponseStatus(200)
            ->assertResponseJson([
                'code' => 200,
                'message' => '删除成功'
            ]);

        // 验证数据库是否删除成功
        $this->assertDatabaseMissing('admin_role', [
            'id' => $role->id
        ]);
    }

    public function testCannotDeleteSuperAdminRole()
    {
        $this->withHeader('Authorization', $this->token)
            ->delete('/api/v1/admin/roles/1');

        $this->assertResponseStatus(400)
            ->assertResponseJson([
                'code' => 400,
                'message' => '不能删除超级管理员角色'
            ]);
    }
} 