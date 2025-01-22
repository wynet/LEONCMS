<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Category extends Model
{
    // 设置表名
    protected $name = 'category';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'parent_id'   => 'int',
        'sort'        => 'int',
        'status'      => 'int',
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
        'name',
        'parent_id',
        'sort',
        'status',
        'status_text',
        'create_time',
        'update_time',
        'children'    // 子栏目数据
    ];

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }

    // 获取所有子栏目ID
    public function getChildrenIds($id)
    {
        $ids = [$id];
        $children = $this->where('parent_id', $id)->column('id');
        
        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getChildrenIds($childId));
        }
        
        return $ids;
    }
} 