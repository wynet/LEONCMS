<?php

use think\facade\Route;

// 管理后台API
Route::group('admin', function () {
    // 登录相关
    Route::post('login', 'admin.Login/login');
    Route::post('logout', 'admin.Login/logout');
    
    // 需要登录的接口
    Route::group('', function () {
        // 获取管理员信息
        Route::get('info', 'admin.Admin/info');
        // 修改密码
        Route::post('password', 'admin.Admin/password');
        
        // 角色管理
        Route::resource('roles', 'admin.Role');
        // 权限管理
        Route::resource('permissions', 'admin.Permission');
        // 管理员管理
        Route::resource('admins', 'admin.Admin');
        // 操作日志
        Route::get('logs', 'admin.Log/index');
        
    })->middleware([
        \app\middleware\AdminAuth::class,
        \app\middleware\AdminPermission::class,
        \app\middleware\AdminLog::class
    ]);
    
})->prefix('app\controller\\'); 