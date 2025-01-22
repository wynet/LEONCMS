<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminRoleValidate extends Validate
{
    protected $rule = [
        'name'           => 'require|length:2,50|unique:admin_role',
        'description'    => 'max:255',
        'status'        => 'in:0,1',
        'sort'          => 'number|between:0,9999',
        'permission_ids' => 'array',
    ];

    protected $message = [
        'name.require'        => '角色名称不能为空',
        'name.length'         => '角色名称长度必须在2-50个字符之间',
        'name.unique'         => '角色名称已存在',
        'description.max'     => '角色描述最多不能超过255个字符',
        'status.in'           => '状态值只能是0或1',
        'sort.number'         => '排序必须是数字',
        'sort.between'        => '排序必须在0-9999之间',
        'permission_ids.array' => '权限ID必须是数组',
    ];

    protected $scene = [
        'create' => ['name', 'description', 'status', 'sort', 'permission_ids'],
        'update' => ['name', 'description', 'status', 'sort', 'permission_ids'],
    ];
} 