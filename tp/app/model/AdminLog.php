<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class AdminLog extends Model
{
    // 设置表名
    protected $name = 'admin_log';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'admin_id'    => 'int',
        'path'        => 'string',
        'method'      => 'string',
        'ip'          => 'string',
        'content'     => 'string',
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
        'path',
        'method',
        'ip',
        'content',
        'create_time',
        'admin'        // 关联数据
    ];

    // 关联管理员
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id')
            ->bind([
                'admin_name' => 'username'
            ])
            ->visible(['id', 'username', 'nickname']);
    }
    
    // 格式化请求参数
    public function getInputAttr($value)
    {
        return $value ? json_decode($value, true) : [];
    }
    
    public function setInputAttr($value)
    {
        return $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : '';
    }
} 