<?php
declare(strict_types=1);

namespace app\model;

// 继承BaseModel而不是直接继承think\Model
class Article extends BaseModel
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
        'update_time' => 'datetime',
        'delete_time' => 'datetime',
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 设置时间字段格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 设置允许显示的字段（白名单）
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

    // 设置隐藏的字段（黑名单）
    protected $hidden = [
        'delete_time',   // 隐藏软删除时间
        'user_id',       // 隐藏用户ID
        'category_id',   // 隐藏分类ID
    ];

    // 允许修改的字段
    protected $allowModifyFields = ['title', 'content', 'status'];

    // 关联分类时只返回需要的字段
    public function category()
    {
        return $this->belongsTo(Category::class)
            ->bind([
                'category_name' => 'name'
            ])
            ->visible(['id', 'name'])  // 只显示分类的id和name
            ->hidden(['create_time', 'update_time', 'delete_time']);
    }

    // 关联作者时只返回需要的字段
    public function author()
    {
        return $this->belongsTo(Admin::class, 'user_id')
            ->bind([
                'author_name' => 'nickname'
            ])
            ->visible(['id', 'nickname'])  // 只显示作者的id和昵称
            ->hidden(['password', 'salt', 'create_time', 'update_time', 'delete_time']);
    }

    // 可以添加获取器来格式化某些字段
    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
    }

    public function getUpdateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
    }

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '草稿', 1 => '发布'];
        return $status[$data['status']] ?? '';
    }
} 