<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,20|alphaNum',
        'password' => 'require|length:6,20',
        'old_password' => 'require|length:6,20',
        'new_password' => 'require|length:6,20|different:old_password',
        'confirm_password' => 'require|confirm:new_password',
        'verify_code' => 'require|length:6',
        'nickname' => 'require|length:2,20',
        'avatar' => 'url',
        'status' => 'require|in:0,1',
        'role_id' => 'require|integer|gt:0'
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.length' => '用户名长度必须在4-20个字符之间',
        'username.alphaNum' => '用户名只能是字母和数字',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度必须在6-20个字符之间',
        'old_password.require' => '原密码不能为空',
        'old_password.length' => '原密码长度必须在6-20个字符之间',
        'new_password.require' => '新密码不能为空',
        'new_password.length' => '新密码长度必须在6-20个字符之间',
        'new_password.different' => '新密码不能与原密码相同',
        'confirm_password.require' => '请确认新密码',
        'confirm_password.confirm' => '两次输入的密码不一致',
        'verify_code.require' => '请输入验证码',
        'verify_code.length' => '验证码长度必须是6位',
        'nickname.require' => '昵称不能为空',
        'nickname.length' => '昵称长度必须在2-20个字符之间',
        'avatar.url' => '头像必须是有效的URL地址',
        'status.require' => '状态不能为空',
        'status.in' => '状态值只能是0或1',
        'role_id.require' => '角色ID不能为空',
        'role_id.integer' => '角色ID必须是整数',
        'role_id.gt' => '角色ID必须大于0'
    ];

    protected $scene = [
        'create' => ['username', 'password', 'nickname', 'avatar', 'status', 'role_id'],
        'update' => ['nickname', 'avatar', 'status', 'role_id'],
        'password' => ['old_password', 'new_password', 'confirm_password', 'verify_code']
    ];
} 