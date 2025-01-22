<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\model\User;

class CreateTestUser extends Command
{
    protected function configure()
    {
        $this->setName('create:testuser')
            ->setDescription('Create test user');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $user = new User;
            $user->username = 'test';
            $user->password = 'password'; // 会自动通过模型的修改器加密
            $user->email = 'test@example.com';
            $user->status = 1;
            $user->save();
            
            $output->writeln("Test user created successfully!");
            $output->writeln("Username: test");
            $output->writeln("Password: password");
            
        } catch (\Exception $e) {
            $output->writeln("<error>Error: " . $e->getMessage() . "</error>");
        }
    }
} 