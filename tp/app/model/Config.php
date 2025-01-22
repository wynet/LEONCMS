<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Config extends Model
{
    // 设置表名
    protected $name = 'config';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'key'         => 'string',
        'value'       => 'string',
        'title'       => 'string',
        'group'       => 'string',
        'type'        => 'string',
        'extra'       => 'string',
        'description' => 'string',
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
        'key',
        'value',
        'title',
        'group',
        'type',
        'extra',
        'description',
        'status',
        'status_text'
    ];

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }

    // 获取器：处理额外选项
    public function getExtraAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // 修改器：处理额外选项
    public function setExtraAttr($value)
    {
        return $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : '';
    }
} 