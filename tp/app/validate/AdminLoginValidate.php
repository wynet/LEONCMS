<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminLoginValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,20',
        'password' => 'require|length:6,20',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.length' => '用户名长度必须在4-20个字符之间',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度必须在6-20个字符之间',
    ];
} 