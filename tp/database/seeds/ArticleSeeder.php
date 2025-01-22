<?php
declare(strict_types=1);

use think\migration\Seeder;
use think\facade\Db;

class ArticleSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * @return void
     */
    public function run(): void
    {
        // 先禁用外键检查
        $this->adapter->getConnection()->exec('SET FOREIGN_KEY_CHECKS=0');
        
        // 清空文章表
        $this->adapter->getConnection()->exec('TRUNCATE TABLE article');
        
        // 重新启用外键检查
        $this->adapter->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1');

        // 创建测试文章数据
        $data = [
            [
                'title' => '测试文章1',
                'content' => '这是测试文章1的内容',
                'description' => '测试文章1的描述',
                'keywords' => '测试,文章1',
                'category_id' => 1,
                'user_id' => 1,
                'cover' => '',
                'status' => 1,
                'view_count' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'title' => '测试文章2',
                'content' => '这是测试文章2的内容',
                'description' => '测试文章2的描述',
                'keywords' => '测试,文章2',
                'category_id' => 1,
                'user_id' => 1,
                'cover' => '',
                'status' => 1,
                'view_count' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ]
        ];

        // 插入数据
        Db::name('article')->insertAll($data);
    }
} 