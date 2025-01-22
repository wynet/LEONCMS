<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Role extends Model
{
    // 设置表名
    protected $name = 'admin_role';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
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
        'description',
        'status',
        'status_text'
    ];

    // 关联权限
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }
} 