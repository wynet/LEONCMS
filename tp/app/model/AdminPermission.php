<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class AdminPermission extends Model
{
    protected $pk = 'id';
    protected $table = 'admin_permission';
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 类型转换
    protected $type = [
        'status'    => 'integer',
        'sort'      => 'integer',
        'parent_id' => 'integer',
    ];
    
    // 关联角色
    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_permission', 'permission_id', 'role_id');
    }
    
    // 关联子权限
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->order('sort', 'asc');
    }
    
    // 关联父权限
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
} 