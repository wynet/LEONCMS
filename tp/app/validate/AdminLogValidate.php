<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminLogValidate extends Validate
{
    protected $rule = [
        'admin_id'    => 'require|number',
        'path'        => 'require|max:100',
        'method'      => 'require|in:GET,POST,PUT,DELETE',
        'ip'          => 'require|ip',
        'input'       => 'array',
        'start_time'  => 'date',
        'end_time'    => 'date|egt:start_time',
    ];

    protected $message = [
        'admin_id.require'   => '管理员ID不能为空',
        'admin_id.number'    => '管理员ID必须是数字',
        'path.require'       => '请求路径不能为空',
        'path.max'           => '请求路径最多不能超过100个字符',
        'method.require'     => '请求方法不能为空',
        'method.in'          => '请求方法只能是GET、POST、PUT、DELETE',
        'ip.require'         => 'IP地址不能为空',
        'ip.ip'             => 'IP地址格式不正确',
        'input.array'        => '请求参数必须是数组',
        'start_time.date'    => '开始时间格式不正确',
        'end_time.date'      => '结束时间格式不正确',
        'end_time.egt'       => '结束时间必须大于等于开始时间',
    ];

    protected $scene = [
        'search' => ['admin_id', 'path', 'method', 'ip', 'start_time', 'end_time'],
    ];
} 