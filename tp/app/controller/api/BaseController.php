<?php
declare(strict_types=1);

namespace app\controller\api;

use think\App;
use think\exception\ValidateException;
use think\Response;
use think\response\Json;

abstract class BaseController
{
    protected $app;
    protected $request;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        
        // 跨域请求支持
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        
        // 初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 返回成功消息
     */
    protected function success($data = [], string $message = 'success', int $code = 200): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 返回错误消息
     */
    protected function error(string $message = '', int $code = 400, $data = []): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 验证数据
     */
    protected function validate(array $data, string $validate, array $message = [], bool $batch = false)
    {
        try {
            $v = new $validate;
            $v->batch($batch)->check($data);
        } catch (ValidateException $e) {
            throw new ValidateException($e->getMessage());
        }
    }
} 