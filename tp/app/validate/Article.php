<?php
declare(strict_types=1);

namespace app\validate;

class Article extends BaseValidate
{
    protected $rule = [
        'id'      => 'require|number',
        'title'   => 'require|max:255',
        'content' => 'require',
        'status'  => 'in:0,1',
    ];

    protected $message = [
        'id.require'      => '文章ID不能为空',
        'id.number'       => '文章ID必须是数字',
        'title.require'   => '标题不能为空',
        'title.max'       => '标题最多不能超过255个字符',
        'content.require' => '内容不能为空',
        'status.in'       => '状态值只能是0或1',
    ];

    protected $scene = [
        'create' => ['title', 'content', 'status'],
        'update' => ['id', 'title', 'content', 'status'],
        'delete' => ['id'],
    ];
} 