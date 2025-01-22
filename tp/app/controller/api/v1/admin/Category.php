<?php
declare(strict_types=1);

namespace app\controller\api\v1\admin;

use app\BaseController;
use app\model\Category as CategoryModel;
use app\validate\CategoryValidate;
use think\exception\ValidateException;

class Category extends BaseController
{
    /**
     * 栏目列表
     */
    public function index()
    {
        // 获取所有栏目
        $list = CategoryModel::field('id, name, parent_id, sort, status, create_time, update_time')
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->append(['status_text'])
            ->toArray();

        // 构建树形结构
        $tree = $this->buildTree($list);

        return $this->success([
            'list' => $tree
        ]);
    }

    /**
     * 创建栏目
     */
    public function save()
    {
        // 验证输入
        try {
            validate(CategoryValidate::class)
                ->scene('create')
                ->check($this->request->post());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->post());

        try {
            // 创建栏目
            $category = CategoryModel::create($data);
            
            // 重新获取完整信息
            $category = CategoryModel::find($category->id);
            
            return $this->success([
                'category' => $category->append(['status_text'])
            ], '创建成功');
        } catch (\Exception $e) {
            return $this->error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 更新栏目
     */
    public function update($id)
    {
        // 验证输入
        try {
            validate(CategoryValidate::class)
                ->scene('update')
                ->check(['id' => $id] + $this->request->put());
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找栏目
        $category = CategoryModel::find($id);
        if (!$category) {
            return $this->error('栏目不存在');
        }

        // XSS过滤
        $data = array_map(function($value) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }, $this->request->put());

        try {
            // 更新栏目
            $category->save($data);
            
            // 重新获取完整信息
            $category = CategoryModel::find($id);
            
            return $this->success([
                'category' => $category->append(['status_text'])
            ], '更新成功');
        } catch (\Exception $e) {
            return $this->error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 删除栏目
     */
    public function delete($id)
    {
        // 验证输入
        try {
            validate(CategoryValidate::class)
                ->scene('delete')
                ->check(['id' => $id]);
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        }

        // 查找栏目
        $category = CategoryModel::find($id);
        if (!$category) {
            return $this->error('栏目不存在');
        }

        // 检查是否有子栏目
        $hasChildren = CategoryModel::where('parent_id', $id)->find();
        if ($hasChildren) {
            return $this->error('请先删除子栏目');
        }

        // 检查是否有关联的文章
        $hasArticles = \app\model\Article::where('category_id', $id)->find();
        if ($hasArticles) {
            return $this->error('请先删除该栏目下的文章');
        }

        try {
            // 删除栏目
            $category->delete();
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error('删除失败：' . $e->getMessage());
        }
    }

    /**
     * 构建树形结构
     */
    protected function buildTree($list, $parentId = 0)
    {
        $tree = [];
        foreach ($list as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($list, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }
} 