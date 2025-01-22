<?php
declare(strict_types=1);

namespace tests;

use think\App;
use think\facade\Config;
use think\facade\Db;
use think\facade\Route;
use think\Response;

ini_set('memory_limit', '1024M');  // 设置为 1GB

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $app;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 加载助手函数
        if (is_file(__DIR__ . '/../vendor/topthink/framework/src/helper.php')) {
            require_once __DIR__ . '/../vendor/topthink/framework/src/helper.php';
        }
        
        // 创建应用
        $this->app = new App();
        
        // 加载配置
        $this->initConfig();
        
        // 初始化应用
        $this->app->initialize();
        
        // 加载路由
        $this->loadRoutes();
        
        // 清理缓存
        $this->app->cache->clear();
    }

    /**
     * 初始化配置
     */
    protected function initConfig()
    {
        // 应用配置
        Config::set([
            'app' => [
                'debug' => true,
                'default_timezone' => 'Asia/Shanghai',
                'show_error_msg' => true,
                'exception_handle' => null, // 禁用异常处理，以便看到原始错误
            ],
            'log' => [
                'level' => ['error', 'warning', 'info', 'debug'],
                'type' => 'File',
                'path' => $this->app->getRuntimePath() . 'log' . DIRECTORY_SEPARATOR, // 使用 getRuntimePath() 方法
            ],
        ], 'config');

        // 数据库配置
        Config::set([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => [
                        'type' => 'mysql',
                        'hostname' => 'localhost',
                        'database' => 'cms_81100_net',
                        'username' => 'cms_81100_net',
                        'password' => '4oVJphpW',
                        'charset' => 'utf8mb4',
                        'prefix' => '',
                        'debug' => true,
                    ],
                ],
            ],
        ], 'config');

        // 中间件配置
        Config::set([
            'middleware' => [
                'alias' => [
                    'api_response' => \app\middleware\ApiResponse::class,
                    'admin_auth' => \app\middleware\AdminAuth::class,
                    'admin_permission' => \app\middleware\AdminPermission::class,
                    'admin_log' => \app\middleware\AdminLog::class,
                ],
                'global' => ['api_response'],
            ],
        ], 'config');
    }

    /**
     * 加载路由
     */
    protected function loadRoutes()
    {
        try {
            $routePath = $this->app->getRootPath() . 'route' . DIRECTORY_SEPARATOR;
            $files = glob($routePath . '*.php');
            
            foreach ($files as $file) {
                include $file;
            }
            
        } catch (\Throwable $e) {
            echo "Error loading routes: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 发送GET请求
     */
    protected function get($uri, $data = [])
    {
        return $this->request('GET', $uri, $data);
    }

    /**
     * 发送POST请求
     */
    protected function post($uri, $data = [])
    {
        return $this->request('POST', $uri, $data);
    }

    /**
     * 发送PUT请求
     */
    protected function put($uri, $data = [])
    {
        return $this->request('PUT', $uri, $data);
    }

    /**
     * 发送DELETE请求
     */
    protected function delete($uri, $data = [])
    {
        return $this->request('DELETE', $uri, $data);
    }

    /**
     * 发送请求
     */
    protected function request($method, $uri, $data = [])
    {
        try {
            // 创建请求
            $request = $this->app->request;
            $request->setMethod($method);
            $request->setPathinfo($uri);
            
            // 设置 JSON 请求头
            $request->withHeader(['Content-Type' => 'application/json']);
            
            if ($method === 'GET') {
                $request->withGet($data);
            } else {
                if ($method === 'PUT' || $method === 'DELETE') {
                    $request->withHeader(['X-HTTP-Method-Override' => $method]);
                    $method = 'POST';
                }
                // 将数据转换为 JSON
                $request->withInput(json_encode($data));
            }
            
            // 运行应用
            $response = $this->app->http->run($request);
            
            // 解析响应内容
            $content = $response->getContent();
            if (is_string($content)) {
                $data = json_decode($content, true) ?: [];
                if (isset($data['code'])) {
                    $response->code($data['code']);
                }
            }
            
            $this->response = $response;
            return $response;
            
        } catch (\Throwable $e) {
            // 记录异常并返回 500 响应
            $result = [
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => [],
                'debug' => [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request' => [
                        'method' => $method,
                        'uri' => $uri,
                        'data' => $data,
                    ],
                    'app_path' => $this->app->getBasePath(),
                    'route_info' => Route::getName(),
                    'loaded_files' => get_included_files()
                ]
            ];
            $this->response = json($result);
            return $this->response;
        }
    }

    /**
     * 设置请求头
     */
    protected function withHeader($name, $value)
    {
        $this->app->request->withHeader([$name => $value]);
        return $this;
    }

    /**
     * 验证数据库中是否存在记录
     */
    protected function assertDatabaseHas($table, array $data)
    {
        $found = Db::table($table)->where($data)->find();
        $this->assertNotNull($found, "Failed asserting that table [{$table}] contains record");
    }

    /**
     * 验证数据库中是否不存在记录
     */
    protected function assertDatabaseMissing($table, array $data)
    {
        $found = Db::table($table)->where($data)->find();
        $this->assertNull($found, "Failed asserting that table [{$table}] does not contain record");
    }

    /**
     * 获取响应数据
     */
    protected function getResponseData()
    {
        $content = $this->response->getContent();
        if (is_string($content)) {
            return json_decode($content, true) ?: [];
        }
        return $content ?: [];
    }

    /**
     * 断言响应状态码
     */
    protected function assertResponseStatus($status)
    {
        $data = $this->getResponseData();
        $code = $data['code'] ?? 500;
        $this->assertEquals($status, $code, sprintf(
            'Expected response status code %d but got %d. Response: %s',
            $status,
            $code,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        ));
        return $this;
    }

    /**
     * 断言响应JSON数据
     */
    protected function assertResponseJson(array $data)
    {
        $responseData = $this->getResponseData();
        $this->assertArraySubset($data, $responseData);
        return $this;
    }

    /**
     * 断言响应JSON结构
     */
    protected function assertResponseJsonStructure(array $structure)
    {
        $responseData = $this->getResponseData();
        $this->assertStructure($structure, $responseData);
        return $this;
    }

    /**
     * 验证数组结构
     */
    protected function assertStructure(array $structure, array $responseData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                // 验证数组中的每个元素
                foreach ($responseData as $item) {
                    $this->assertStructure($structure['*'], $item);
                }
            } elseif (is_array($value)) {
                // 验证嵌套结构
                $this->assertArrayHasKey($key, $responseData);
                $this->assertIsArray($responseData[$key]);
                $this->assertStructure($value, $responseData[$key]);
            } else {
                // 验证键是否存在
                $this->assertArrayHasKey($value, $responseData);
            }
        }
    }

    /**
     * 断言数组是否为另一个数组的子集
     */
    protected function assertArraySubset($subset, $array)
    {
        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            if (is_array($value)) {
                $this->assertArraySubset($value, $array[$key]);
            } else {
                $this->assertEquals($value, $array[$key]);
            }
        }
    }
} 


