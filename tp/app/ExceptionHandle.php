<?php
declare(strict_types=1);

namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\facade\Log;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            $data = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'code' => $this->getCode($exception),
            ];
            $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            
            // 记录异常日志
            Log::error($log);
            
            if ($exception instanceof \Exception) {
                // 添加额外的异常信息
                Log::error($exception->getTraceAsString());
            }
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 记录日志
        $this->report($e);
        
        // API请求返回JSON格式的错误信息
        if ($request->isAjax() || $request->isJson()) {
            return json([
                'code' => $this->getCode($e),
                'message' => $this->getMessage($e),
                'data' => env('APP_DEBUG') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ] : []
            ]);
        }

        return parent::render($request, $e);
    }

    /**
     * 获取错误编码
     * @param Throwable $exception
     * @return int
     */
    protected function getCode(Throwable $exception): int
    {
        $code = $exception->getCode();
        if ($code <= 0) {
            return 500;
        }
        return $code;
    }

    /**
     * 获取错误信息
     * @param Throwable $exception
     * @return string
     */
    protected function getMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();
        if (empty($message)) {
            return '服务器错误';
        }
        return $message;
    }
}
