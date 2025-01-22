<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class AdminRole extends Model
{
    protected $pk = 'id';
    protected $table = 'admin_role';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 类型转换
    protected $type = [
        'status' => 'integer',
        'sort'   => 'integer',
    ];
    
    // 关联权限
    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_role_permission', 'role_id', 'permission_id');
    }
    
    // 关联管理员
    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
} 