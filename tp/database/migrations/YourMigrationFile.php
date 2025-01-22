<?php

use Phinx\Migration\AbstractMigration;

class YourMigrationFile extends AbstractMigration
{
    public function change()
    {
        // ... 现有代码 ...

        // 错误的方式
        // $table->getLastInsertId();

        // 正确的方式
        $this->getAdapter()->getConnection()->lastInsertId();

        // ... 现有代码 ...
    }

    public function up()
    {
        // ... 现有代码 ...
        
        $table = $this->table('your_table_name');
        $table->insert([
            'column1' => 'value1',
            'column2' => 'value2'
        ])->save();
        
        $lastInsertId = $this->getAdapter()->getConnection()->lastInsertId();
        
        // ... 现有代码 ...
    }
} 