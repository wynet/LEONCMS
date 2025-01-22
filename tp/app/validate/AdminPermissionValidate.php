<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class AdminPermissionValidate extends Validate
{
    protected $rule = [
        'name'      => 'require|length:2,50',
        'path'      => 'require|length:1,100',
        'method'    => 'require|in:GET,POST,PUT,DELETE',
        'parent_id' => 'number|egt:0',
        'type'      => 'require|in:menu,button,api',
        'icon'      => 'max:50',
        'component' => 'max:100',
        'sort'      => 'number|between:0,9999',
        'status'    => 'in:0,1',
    ];

    protected $message = [
        'name.require'     => '权限名称不能为空',
        'name.length'      => '权限名称长度必须在2-50个字符之间',
        'path.require'     => '权限路径不能为空',
        'path.length'      => '权限路径长度必须在1-100个字符之间',
        'method.require'   => '请求方法不能为空',
        'method.in'        => '请求方法只能是GET、POST、PUT、DELETE',
        'parent_id.number' => '父级ID必须是数字',
        'parent_id.egt'    => '父级ID必须大于等于0',
        'type.require'     => '权限类型不能为空',
        'type.in'          => '权限类型只能是menu、button、api',
        'icon.max'         => '图标最多不能超过50个字符',
        'component.max'    => '前端组件最多不能超过100个字符',
        'sort.number'      => '排序必须是数字',
        'sort.between'     => '排序必须在0-9999之间',
        'status.in'        => '状态值只能是0或1',
    ];

    protected $scene = [
        'create' => ['name', 'path', 'method', 'parent_id', 'type', 'icon', 'component', 'sort', 'status'],
        'update' => ['name', 'path', 'method', 'parent_id', 'type', 'icon', 'component', 'sort', 'status'],
    ];
} 