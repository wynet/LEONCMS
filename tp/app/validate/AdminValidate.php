<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'id' => 'require|number|gt:0',
        'username' => 'require|alphaDash|length:4,20|unique:admin',
        'password' => 'require|length:6,20',
        'old_password' => 'require|length:6,20',
        'confirm_password' => 'require|confirm:password',
        'nickname' => 'max:50',
        'avatar' => 'url',
        'role_id' => 'require|number|gt:0',
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require' => 'ID不能为空',
        'id.number' => 'ID必须是数字',
        'id.gt' => 'ID必须大于0',
        'username.require' => '用户名不能为空',
        'username.alphaDash' => '用户名只能包含字母、数字、下划线和横杠',
        'username.length' => '用户名长度必须在4-20个字符之间',
        'username.unique' => '用户名已存在',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度必须在6-20个字符之间',
        'old_password.require' => '旧密码不能为空',
        'old_password.length' => '旧密码长度必须在6-20个字符之间',
        'confirm_password.require' => '确认密码不能为空',
        'confirm_password.confirm' => '两次输入的密码不一致',
        'nickname.max' => '昵称最多50个字符',
        'avatar.url' => '头像必须是有效的URL地址',
        'role_id.require' => '角色不能为空',
        'role_id.number' => '角色ID必须是数字',
        'role_id.gt' => '角色ID必须大于0',
        'status.require' => '状态不能为空',
        'status.in' => '状态值不正确'
    ];

    protected $scene = [
        'create' => ['username', 'password', 'confirm_password', 'nickname', 'avatar', 'role_id', 'status'],
        'update' => ['id', 'username', 'nickname', 'avatar', 'role_id', 'status'],
        'delete' => ['id'],
        'password' => ['old_password', 'password', 'confirm_password']
    ];
} 