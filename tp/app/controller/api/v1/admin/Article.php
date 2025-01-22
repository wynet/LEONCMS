<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Article as ArticleModel;
use app\validate\ArticleValidate;
use think\exception\ValidateException;

class Article extends BaseController
{
    /**
     * 文章列表
     */
    public function index()
    {
        // 验证输入参数
        try {
            validate(ArticleValidate::class)
                ->scene('index')
                ->check($this->request->only([
                    'page', 'pageSize', 'keyword', 'category_id', 'status'
                ]));
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 设置默认值
        $page = (int)($this->request->param('page', 1));
        $pageSize = (int)($this->request->param('pageSize', 15));

        // 构建查询
        $query = ArticleModel::with(['category', 'author']);

        // 关键词搜索
        if ($keyword = $this->request->param('keyword')) {
            $query->where('title|description|keywords', 'like', "%{$keyword}%");
        }

        // 分类筛选
        if ($categoryId = $this->request->param('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 状态筛选
        if (($status = $this->request->param('status')) !== null) {
            $query->where('status', $status);
        }

        // 获取数据
        $total = $query->count();
        $list = $query->page($page, $pageSize)
            ->order('create_time', 'desc')
            ->select()
            ->append(['status_text'])
            ->toArray();

        return $this->success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ]);
    }

    /**
     * 创建文章
     */
    public function create()
    {
        // 验证输入
        try {
            validate(ArticleValidate::class)
                ->scene('create')
                ->check($this->request->post());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->post());

        // 添加作者ID
        $data['user_id'] = $this->getUserId();
        $data['view_count'] = 0;

        try {
            // 创建文章
            $article = ArticleModel::create($data);
            
            // 重新获取完整信息
            $article = ArticleModel::with(['category', 'author'])
                ->find($article->id)
                ->append(['status_text']);
            
            return $this->success([
                'article' => $article
            ], '创建成功');
        } catch (\Exception $e) {
            return $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新文章
     */
    public function update($id)
    {
        // 验证输入
        try {
            validate(ArticleValidate::class)
                ->scene('update')
                ->check(['id' => $id] + $this->request->put());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找文章
        $article = ArticleModel::find($id);
        if (!$article) {
            return $this->error('文章不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->put());

        try {
            // 更新文章
            $article->save($data);
            
            // 重新获取完整信息
            $article = ArticleModel::with(['category', 'author'])
                ->find($id)
                ->append(['status_text']);
            
            return $this->success([
                'article' => $article
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 删除文章
     */
    public function delete($id)
    {
        // 验证输入
        try {
            validate(ArticleValidate::class)
                ->scene('delete')
                ->check(['id' => $id]);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找文章
        $article = ArticleModel::find($id);
        if (!$article) {
            return $this->error('文章不存在');
        }

        try {
            // 删除文章
            $article->delete();
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 获取当前登录用户ID
     */
    protected function getUserId()
    {
        // 从登录信息中获取用户ID
        return $this->request->user['id'] ?? 0;
    }

    /**
     * 成功响应
     * @param array $data 响应数据
     * @param string $message 响应信息
     * @param int $code 响应码
     * @return \think\Response
     */
    protected function success($data = [], string $message = 'success', int $code = 200): \think\Response
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 错误响应
     * @param string $message 错误信息
     * @param int $code 错误码
     * @param mixed $data 错误数据
     * @return \think\Response
     */
    protected function error(string $message = '', int $code = 400, $data = []): \think\Response
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
} 