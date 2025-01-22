<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateArticleTable extends Migrator
{
    public function change()
    {
        $this->table('article', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false, 'comment' => '文章标题'])
            ->addColumn('content', 'text', ['comment' => '文章内容'])
            ->addColumn('description', 'string', ['limit' => 500, 'null' => true, 'comment' => '文章描述'])
            ->addColumn('keywords', 'string', ['limit' => 255, 'null' => true, 'comment' => '关键词'])
            ->addColumn('category_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '分类ID'])
            ->addColumn('user_id', 'integer', ['signed' => false, 'null' => false, 'comment' => '作者ID'])
            ->addColumn('cover', 'string', ['limit' => 255, 'null' => true, 'comment' => '封面图'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('view_count', 'integer', ['signed' => false, 'default' => 0, 'comment' => '浏览次数'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['category_id'])
            ->addIndex(['user_id'])
            ->addIndex(['status'])
            ->addIndex(['view_count'])
            ->create();
    }
} 