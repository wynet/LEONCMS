<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCategoryTable extends Migrator
{
    public function change()
    {
        $this->table('category', ['engine' => 'InnoDB', 'collation' => 'utf8mb4_unicode_ci'])
            ->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'comment' => '分类名称'])
            ->addColumn('parent_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '父级ID'])
            ->addColumn('sort', 'integer', ['signed' => false, 'default' => 0, 'comment' => '排序'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '状态：0禁用 1启用'])
            ->addColumn('create_time', 'datetime', ['null' => true])
            ->addColumn('update_time', 'datetime', ['null' => true])
            ->addIndex(['parent_id'])
            ->addIndex(['sort'])
            ->create();
    }
} 