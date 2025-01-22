<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Article extends Model
{
    // 设置表名
    protected $name = 'article';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'title'       => 'string',
        'content'     => 'string',
        'description' => 'string',
        'keywords'    => 'string',
        'category_id' => 'int',
        'user_id'     => 'int',
        'cover'       => 'string',
        'status'      => 'int',
        'view_count'  => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 设置时间字段格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 设置允许显示的字段
    protected $visible = [
        'id', 
        'title',
        'description',
        'keywords',
        'content',
        'cover',
        'status',
        'status_text',
        'view_count',
        'create_time',
        'update_time',
        'category',      // 关联数据
        'author'        // 关联数据
    ];

    // 关联分类时只返回需要的字段
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->bind([
                'category_name' => 'name'
            ])
            ->visible(['id', 'name']);
    }

    // 关联作者时只返回需要的字段
    public function author()
    {
        return $this->belongsTo(Admin::class, 'user_id')
            ->bind([
                'author_name' => 'nickname'
            ])
            ->visible(['id', 'nickname']);
    }

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '草稿', 1 => '发布'];
        return $status[$data['status']] ?? '';
    }
} 