<?php
declare(strict_types=1);

use think\migration\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run Method.
     *
     * @return void
     */
    public function run(): void
    {
        $this->getAdapter()->execute('TRUNCATE TABLE category');
        
        $data = [
            [
                'id' => 1,
                'name' => '公司新闻',
                'parent_id' => 0,
                'sort' => 100,
                'status' => 1
            ],
            [
                'id' => 2, 
                'name' => '行业动态',
                'parent_id' => 0,
                'sort' => 90,
                'status' => 1
            ],
            [
                'id' => 3,
                'name' => '技术分享',
                'parent_id' => 0,
                'sort' => 80,
                'status' => 1
            ]
        ];
        
        $this->table('category')->insert($data)->save();
    }
} 