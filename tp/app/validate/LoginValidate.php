<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'code'     => 'require|length:6',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'password.require' => '密码不能为空',
        'code.require'    => '验证码不能为空',
        'code.length'     => '验证码必须是6位数字',
    ];

    protected $scene = [
        'login'      => ['username', 'password', 'code'],
        'getCode'    => ['username'],
    ];
} 