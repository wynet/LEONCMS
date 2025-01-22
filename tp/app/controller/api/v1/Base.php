<?php
declare(strict_types=1);

namespace app\controller\api\v1;

use app\BaseController;
use app\model\Config as ConfigModel;
use think\exception\ValidateException;

class Base extends BaseController
{
    /**
     * 获取站点基本信息
     */
    public function info()
    {
        try {
            // 获取站点配置
            $config = ConfigModel::where('status', 1)
                ->column('value', 'key');

            // 只返回允许公开的配置
            $allowKeys = [
                'site_name',
                'site_logo',
                'site_description',
                'site_keywords',
                'site_icp',
                'site_copyright'
            ];

            $publicConfig = array_intersect_key($config, array_flip($allowKeys));

            return $this->success([
                'config' => $publicConfig
            ]);
        } catch (\Exception $e) {
            return $this->error('获取站点信息失败：' . $e->getMessage());
        }
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        try {
            // 验证上传文件
            $file = $this->request->file('file');
            validate([
                'file' => [
                    'fileSize' => 10 * 1024 * 1024,  // 10MB
                    'fileExt'  => 'jpg,jpeg,png,gif'
                ]
            ])->check(['file' => $file]);

            // 上传到本地服务器
            $savename = \think\facade\Filesystem::disk('public')
                ->putFile('upload', $file);

            if ($savename) {
                return $this->success([
                    'url' => '/storage/' . $savename
                ], '上传成功');
            }

            return $this->error('上传失败');
        } catch (ValidateException $e) {
            return $this->error($e->getMessage());
        } catch (\Exception $e) {
            return $this->error('上传失败：' . $e->getMessage());
        }
    }
} 