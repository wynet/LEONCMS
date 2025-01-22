<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class GeneratePassword extends Command
{
    protected function configure()
    {
        $this->setName('generate:password')
            ->setDescription('Generate hashed password');
    }

    protected function execute(Input $input, Output $output)
    {
        $password = 'password'; // 这里设置你想要的密码
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $output->writeln("Password: " . $password);
        $output->writeln("Hash: " . $hash);
    }
} 