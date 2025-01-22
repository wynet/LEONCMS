<?php
declare (strict_types = 1);

namespace app;

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
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
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
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 成功响应
     */
    protected function success($data = [], string $message = 'success', int $code = 200): Response
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * 错误响应
     */
    protected function error(string $message = '', int $code = 400, $data = []): Response
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * XSS过滤
     * @param array|string $data 需要过滤的数据
     * @return array|string
     */
    protected function filterXSS($data)
    {
        if (is_array($data)) {
            return array_map(function($value) {
                return is_string($value) ? htmlspecialchars($value) : $value;
            }, $data);
        }
        return is_string($data) ? htmlspecialchars($data) : $data;
    }

    /**
     * 获取当前登录用户ID
     * @return int
     */
    protected function getUserId(): int
    {
        return $this->request->user['id'] ?? 0;
    }

    /**
     * 检查权限
     * @param string $permission 权限标识
     * @return bool
     */
    protected function checkPermission(string $permission): bool
    {
        // 超级管理员拥有所有权限
        if ($this->request->user['role_id'] === 1) {
            return true;
        }

        // 获取用户权限列表
        $permissions = $this->request->user['permissions'] ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * 生成唯一标识
     * @return string
     */
    protected function generateUniqueId(): string
    {
        return md5(uniqid((string)mt_rand(), true));
    }
}
