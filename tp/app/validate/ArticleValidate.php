<?php
declare(strict_types=1);

namespace app\validate;

use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'title' => 'require|max:255',
        'content' => 'require',
        'description' => 'max:500',
        'keywords' => 'max:255',
        'category_id' => 'require|integer|gt:0',
        'cover' => 'url',
        'status' => 'require|in:0,1',
        'page' => 'integer|min:1',
        'pageSize' => 'integer|between:1,100',
        'keyword' => 'max:50'
    ];

    protected $message = [
        'id.require' => '文章ID不能为空',
        'id.integer' => '文章ID必须是整数',
        'id.gt' => '文章ID必须大于0',
        'title.require' => '标题不能为空',
        'title.max' => '标题最多255个字符',
        'content.require' => '内容不能为空',
        'description.max' => '描述最多500个字符',
        'keywords.max' => '关键词最多255个字符',
        'category_id.require' => '分类不能为空',
        'category_id.integer' => '分类ID必须为整数',
        'category_id.gt' => '分类ID必须大于0',
        'cover.url' => '封面必须是有效的URL',
        'status.require' => '状态不能为空',
        'status.in' => '状态值无效',
        'page.integer' => '页码必须是整数',
        'page.min' => '页码必须大于0',
        'pageSize.integer' => '每页数量必须是整数',
        'pageSize.between' => '每页数量必须在1-100之间'
    ];

    protected $scene = [
        'index' => [
            'page', 'pageSize', 'keyword',
            'category_id' => 'integer|gt:0',
            'status' => 'in:0,1'
        ],
        'create' => [
            'title', 'content', 'description', 'keywords',
            'category_id', 'cover', 'status'
        ],
        'update' => [
            'id',
            'title' => 'max:255',
            'content' => 'max:65535',
            'description', 'keywords',
            'category_id' => 'integer|gt:0',
            'cover', 
            'status' => 'in:0,1'
        ],
        'delete' => ['id']
    ];
} 