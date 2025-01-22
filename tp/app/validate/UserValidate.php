<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'old_password'  => 'require|length:6,20',
        'new_password'  => 'require|length:6,20|different:old_password',
        'nickname'      => 'max:50',
        'email'         => 'email',
        'avatar'        => 'url',
        'mobile'        => 'mobile|unique:user'
    ];

    protected $message = [
        'old_password.require'     => '请输入原密码',
        'old_password.length'      => '原密码长度必须在6-20个字符之间',
        'new_password.require'     => '请输入新密码',
        'new_password.length'      => '新密码长度必须在6-20个字符之间',
        'new_password.different'   => '新密码不能与原密码相同',
        'nickname.max'             => '昵称最多不能超过50个字符',
        'email.email'              => '邮箱格式不正确',
        'avatar.url'               => '头像必须是有效的URL地址',
        'mobile.mobile'            => '手机号码格式不正确',
        'mobile.unique'            => '该手机号码已被使用'
    ];

    protected $scene = [
        'password' => ['old_password', 'new_password'],
        'update'   => ['nickname', 'email', 'avatar', 'mobile'],
    ];

    protected function mobile($value)
    {
        return preg_match('/^1[3-9]\d{9}$/', $value) === 1;
    }
} 