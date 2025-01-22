<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 默认日志记录通道
    'default'      => 'file',
    // 日志记录级别
    'level'        => ['error', 'warning', 'info', 'debug'],
    // 日志类型记录的通道
    'type_channel' => [
        'error'    => 'file',
        'warning'  => 'file',
        'info'     => 'file',
        'debug'    => 'file',
    ],
    // 关闭全局日志写入
    'close'        => false,
    // 全局日志处理 支持闭包
    'processor'    => null,

    // 日志通道列表
    'channels'     => [
        'file' => [
            // 日志记录方式
            'type'           => 'File',
            // 日志保存目录
            'path'          => runtime_path() . 'log',
            // 单文件日志写入
            'single'        => false,
            // 独立日志级别
            'apart_level'   => ['error', 'info', 'debug'],
            // 最大日志文件数量
            'max_files'     => 30,
            // 日志格式
            'format'        => '%s [%s] %s',
            // 是否实时写入
            'realtime_write' => true,
        ],
        // 其它日志通道配置
    ],

];
