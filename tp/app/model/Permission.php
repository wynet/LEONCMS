<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Permission extends Model
{
    // 设置表名
    protected $name = 'permission';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'path'        => 'string',
        'method'      => 'string',
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
        'name',
        'path',
        'method',
        'description',
        'status',
        'status_text',
        'create_time',
        'update_time'
    ];

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }
} 