<?php
declare(strict_types=1);

namespace app\middleware;

use Closure;
use think\facade\Log;
use think\Request;
use think\Response;

class RouteIdCheck
{
    /**
     * 需要ID检查的路由配置
     * @var array
     */
    protected $routes = [
        'articles' => '文章',
        'admins' => '管理员',
        'roles' => '角色',
        'permissions' => '权限'
    ];

    /**
     * 处理请求
     */
    public function handle($request, Closure $next)
    {
        // 只检查PUT和DELETE请求
        $method = strtoupper($request->method(true));
        if (!in_array($method, ['PUT', 'DELETE'])) {
            return $next($request);
        }

        // 获取请求路径并解析
        $pathParts = explode('/', trim($request->pathinfo(), '/'));
        
        // 检查是否是管理后台路径
        if (count($pathParts) >= 3 && $pathParts[0] === 'api' && $pathParts[1] === 'admin') {
            $resource = $pathParts[2];
            
            // 检查是否是需要ID的资源
            if (isset($this->routes[$resource])) {
                // 检查是否缺少ID
                if (count($pathParts) <= 3) {
                    // 记录拦截信息
                    Log::info('ID check blocked request', [
                        'path' => $request->pathinfo(),
                        'method' => $method,
                        'resource' => $resource,
                        'reason' => '缺少资源ID'
                    ]);
                    
                    return json([
                        'code' => 400,
                        'message' => "请求地址错误，{$method}方法需要指定{$this->routes[$resource]}ID",
                        'data' => null
                    ])->header([
                        'Content-Type' => 'application/json; charset=utf-8'
                    ]);
                }
            }
        }

        return $next($request);
    }
} 