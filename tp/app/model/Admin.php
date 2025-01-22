<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Admin extends Model
{
    // 设置表名
    protected $name = 'admin';
    
    // 设置字段信息
    protected $schema = [
        'id'              => 'int',
        'username'        => 'string',
        'password'        => 'string',
        'nickname'        => 'string',
        'avatar'          => 'string',
        'role_id'         => 'int',
        'status'          => 'int',
        'last_login_time' => 'datetime',
        'create_time'     => 'datetime',
        'update_time'     => 'datetime',
    ];

    // 设置json类型字段
    protected $json = [];

    // 设置追加属性
    protected $append = ['status_text', 'role_name'];

    // 设置隐藏字段
    protected $hidden = [
        'password',           // 隐藏密码
        'update_time',        // 隐藏更新时间
        'role',              // 隐藏关联模型原始数据
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
        'role_id',
        'role_name',
        'role_description',
        'status',
        'status_text',
        'create_time',
        'last_login_time'
    ];

    // 关联角色
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // 获取状态文本
    public function getStatusTextAttr()
    {
        $status = [
            0 => '禁用',
            1 => '启用'
        ];
        return $status[$this->status] ?? '未知';
    }

    // 获取角色名称
    public function getRoleNameAttr()
    {
        return $this->role ? $this->role->name : '';
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