<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class CategoryValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'name' => 'require|max:50',
        'parent_id' => 'integer|egt:0',
        'sort' => 'integer|egt:0',
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require' => 'ID不能为空',
        'id.integer' => 'ID必须是整数',
        'id.gt' => 'ID必须大于0',
        'name.require' => '栏目名称不能为空',
        'name.max' => '栏目名称最多50个字符',
        'parent_id.integer' => '父级ID必须是整数',
        'parent_id.egt' => '父级ID必须大于等于0',
        'sort.integer' => '排序必须是整数',
        'sort.egt' => '排序必须大于等于0',
        'status.require' => '状态不能为空',
        'status.in' => '状态值不正确'
    ];

    protected $scene = [
        'create' => ['name', 'parent_id', 'sort', 'status'],
        'update' => ['id', 'name', 'parent_id', 'sort', 'status'],
        'delete' => ['id']
    ];
} 