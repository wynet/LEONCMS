<?php
declare(strict_types=1);

use think\facade\Route;

/**
 * 管理后台API路由
 * 
 * 包含以下功能：
 * - 文章管理：列表、创建、更新、删除
 * - 管理员管理：列表、创建、更新、删除
 * - 角色管理：列表、创建、更新、删除
 * - 权限管理：列表、创建、更新、删除
 * - 操作日志：查看日志列表
 */
Route::group('api/admin', function () {
    // 定义通用路由参数规则
    $idPattern = ['id' => '\d+'];
    
    // 无需认证的接口
    Route::group('', function () {
        Route::post('login', 'api.v1.admin.Login/login');
        Route::post('logout', 'api.v1.admin.Login/logout');
        Route::get('login/code', 'api.v1.admin.Login/getLoginCode');
    })->middleware([
        \app\middleware\CrossDomain::class,
        \app\middleware\ApiResponse::class,
    ]);
    
    // 需要认证的接口
    Route::group('', function () use ($idPattern) {
        // 管理员信息
        Route::get('info', 'Admin/info');                    // 获取信息
        Route::get('password/code', 'Admin/getPasswordCode'); // 获取验证码
        Route::post('password', 'Admin/password');           // 修改密码
        
        /**
         * 资源管理接口
         */
        // 文章管理
        Route::group('articles', function () use ($idPattern) {
            Route::get('', 'Article/index');                 // 列表
            Route::post('', 'Article/create');               // 创建
            Route::put(':id', 'Article/update')->pattern($idPattern);             // 更新
            Route::delete(':id', 'Article/delete')->pattern($idPattern);          // 删除
        });
        
        // 管理员管理
        Route::group('admins', function () use ($idPattern) {
            Route::get('', 'Admin/index');                         // 列表
            Route::post('', 'Admin/save');                         // 创建
            Route::put(':id', 'Admin/update')->pattern($idPattern);     // 更新
            Route::delete(':id', 'Admin/delete')->pattern($idPattern);  // 删除
        });
        
        // 角色管理
        Route::group('roles', function () use ($idPattern) {
            Route::get('', 'Role/index');                         // 列表
            Route::post('', 'Role/save');                         // 创建
            Route::put(':id', 'Role/update')->pattern($idPattern);     // 更新
            Route::delete(':id', 'Role/delete')->pattern($idPattern);  // 删除
        });
        
        // 权限管理
        Route::group('permissions', function () use ($idPattern) {
            Route::get('', 'Permission/index');                         // 列表
            Route::post('', 'Permission/save');                         // 创建
            Route::put(':id', 'Permission/update')->pattern($idPattern);     // 更新
            Route::delete(':id', 'Permission/delete')->pattern($idPattern);  // 删除
        });
        
        // 栏目管理
        Route::group('categories', function () use ($idPattern) {
            Route::get('', 'Category/index');                         // 列表
            Route::post('', 'Category/save');                         // 创建
            Route::put(':id', 'Category/update')->pattern($idPattern);     // 更新
            Route::delete(':id', 'Category/delete')->pattern($idPattern);  // 删除
        });
        
        /**
         * 系统管理接口
         */
        // 操作日志
        Route::get('logs', 'Log/index');                    // 日志列表
    })->middleware([
        \app\middleware\CrossDomain::class,
        \app\middleware\ApiResponse::class,
        \app\middleware\AdminAuth::class,
        \app\middleware\AdminPermission::class,
        \app\middleware\AdminLog::class,
    ])->prefix('api.v1.admin.');
})->pattern(['id' => '\d+']);

/**
 * 前台API路由
 * 
 * 包含以下功能：
 * - 基础信息：站点信息
 * - 用户功能：登录、退出、个人信息
 * - 文章功能：列表、详情、管理
 */
Route::group('api/:version', function () {
    // 定义通用路由参数规则
    $idPattern = ['id' => '\d+'];
    
    // 基础信息接口
    Route::get('info', 'Base/info');                    // 站点信息
    
    // 前台公开接口
    Route::group('', function () {
        // 用户登录
        Route::post('login', 'Login/login');            // 登录
        Route::post('logout', 'Login/logout');          // 退出
        
        // 文章公开接口
        Route::get('articles', 'Article/index');        // 文章列表
        Route::get('articles/:id', 'Article/read');     // 文章详情
    })->middleware(\app\middleware\ApiResponse::class);
    
    // 需要认证的前台接口
    Route::group('', function () use ($idPattern) {  // 使用 use 引入变量
        // 用户接口
        Route::get('user/info', 'User/info');          // 用户信息
        Route::post('user/password', 'User/password');  // 修改密码
        Route::post('user/update', 'User/update');     // 更新资料
        
        // 文章管理接口
        Route::post('articles', 'Article/save');                       // 创建文章
        Route::put('articles/:id', 'Article/update')->pattern($idPattern);     // 更新文章
        Route::delete('articles/:id', 'Article/delete')->pattern($idPattern);  // 删除文章
    })->middleware([
        \app\middleware\ApiResponse::class,  // API响应格式化
        \app\middleware\ApiAuth::class       // 用户认证
    ]);
})->prefix('app\controller\api\v1\\')
  ->append(['version' => 'v1'])
  ->pattern(['id' => '\d+']);  // 直接使用数组，不使用变量 