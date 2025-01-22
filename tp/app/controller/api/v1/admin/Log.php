<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\AdminLog as AdminLogModel;
use app\validate\LogValidate;
use think\exception\ValidateException;

class Log extends BaseController
{
    /**
     * 日志列表
     */
    public function index()
    {
        // 验证输入参数
        try {
            validate(LogValidate::class)
                ->scene('index')
                ->check($this->request->only([
                    'page', 'pageSize', 'keyword', 'date_range'
                ]));
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 设置默认值
        $page = (int)($this->request->param('page', 1));
        $pageSize = (int)($this->request->param('pageSize', 15));

        // 构建查询
        $query = AdminLogModel::with(['admin']);

        // 关键词搜索
        if ($keyword = $this->request->param('keyword')) {
            $query->where('path|method|ip|content', 'like', "%{$keyword}%")
                ->whereOr('admin_id', 'in', function ($query) use ($keyword) {
                    $query->name('admin')
                        ->where('username|nickname', 'like', "%{$keyword}%")
                        ->field('id');
                });
        }

        // 日期范围筛选
        if ($dateRange = $this->request->param('date_range')) {
            if (is_array($dateRange) && count($dateRange) === 2) {
                $query->whereBetweenTime('create_time', $dateRange[0], $dateRange[1]);
            }
        }

        // 获取数据
        $total = $query->count();
        $list = $query->page($page, $pageSize)
            ->order('create_time', 'desc')
            ->select()
            ->toArray();

        return $this->success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ]);
    }

    /**
     * 记录操作日志
     */
    public static function record($adminId, $path, $method, $content = '')
    {
        try {
            AdminLogModel::create([
                'admin_id' => $adminId,
                'path' => $path,
                'method' => $method,
                'ip' => request()->ip(),
                'content' => $content
            ]);
            return true;
        } catch (\Exception $e) {
            // 记录日志失败不影响主业务
            \think\facade\Log::error('记录操作日志失败：' . $e->getMessage());
            return false;
        }
    }
} 