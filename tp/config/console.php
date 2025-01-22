<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'generate:password' => 'app\command\GeneratePassword',
        'create:testuser' => 'app\command\CreateTestUser',
    ],
];
