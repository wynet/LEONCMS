<?php

declare(strict_types=1);

namespace app\model;

use think\Model;

abstract class BaseModel extends Model
{
    // 开启自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 启用软删除
    use \think\model\concern\SoftDelete;
    protected $deleteTime = 'delete_time';
    
    // 缓存方法示例
    public static function getCacheData($key, $callback, $ttl = 3600)
    {
        try {
            $cache = cache($key);
            if ($cache === false) {
                $cache = $callback();
                cache($key, $cache, $ttl);
            }
            return $cache;
        } catch (\Throwable $e) {
            \think\facade\Log::error('Cache error: ' . $e->getMessage());
            return $callback(); // 缓存失败时直接返回数据
        }
    }

    // 添加通用的查询范围
    public function scopeRecent($query)
    {
        return $query->order($this->getCreateTimeField(), 'desc');
    }

    // 添加通用的数据过滤方法
    protected function filterData(array $data): array
    {
        return array_intersect_key($data, array_flip($this->field));
    }
} 