<?php
declare(strict_types=1);

namespace app\controller;

use think\App;
use think\exception\ValidateException;
use think\Response;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 构造方法
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 初始化方法
     */
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                list($validate, $scene) = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        if ($batch || $this->app->config->get('app.batch_validate')) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 成功响应
     * @param mixed $data 响应数据
     * @param string $message 响应信息
     * @return Response
     */
    protected function success($data = null, string $message = 'success'): Response
    {
        return json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 失败响应
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param mixed $data 响应数据
     * @return Response
     */
    protected function error(string $message = 'error', int $code = 500, $data = null): Response
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
} 