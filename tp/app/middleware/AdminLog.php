<?php
declare(strict_types=1);

namespace app\middleware;

use app\model\AdminLog as AdminLogModel;
use think\facade\Request;

class AdminLog
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        
        // 记录操作日志
        if ($request->isPost() || $request->isPut() || $request->isDelete()) {
            try {
                AdminLogModel::create([
                    'admin_id' => $request->adminData['id'],
                    'path'     => $request->pathinfo(),
                    'method'   => $request->method(),
                    'ip'       => $request->ip(),
                    'input'    => $request->param(),
                ]);
            } catch (\Exception $e) {
                // 记录日志失败不影响正常业务
                \think\facade\Log::error('记录管理员操作日志失败：' . $e->getMessage());
            }
        }
        
        return $response;
    }
} 