<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CleanTables extends Migrator
{
    public function change()
    {
        // 删除可能存在的旧表
        if ($this->hasTable('admin')) {
            $this->dropTable('admin');
        }
        if ($this->hasTable('user')) {
            $this->dropTable('user');
        }
        if ($this->hasTable('article')) {
            $this->dropTable('article');
        }
        if ($this->hasTable('category')) {
            $this->dropTable('category');
        }
    }
} 