<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class User extends Model
{
    // 设置表名
    protected $name = 'user';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'username'    => 'string',
        'password'    => 'string',
        'nickname'    => 'string',
        'avatar'      => 'string',
        'email'       => 'string',
        'mobile'      => 'string',
        'status'      => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime',
        'last_login_time' => 'datetime',
        'last_login_ip'   => 'string'
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 设置时间字段格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 设置允许显示的字段
    protected $visible = [
        'id',
        'username',
        'nickname',
        'avatar',
        'email',
        'mobile',
        'status',
        'status_text',
        'create_time',
        'last_login_time'
    ];

    // 获取器：处理状态
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']] ?? '';
    }

    // 修改器：密码加密
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    // 验证密码
    public function verifyPassword($password): bool
    {
        return password_verify($password, $this->password);
    }
} 