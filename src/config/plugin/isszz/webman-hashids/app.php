<?php

return [
    'enable'  => true,
    
    // 默认连接名称
    'default' => 'main', // 支持bilibili的BV模式

    // Hashids modes
    'modes' => [
        'main' => [
            'salt' => '',
            'length' => 0,
        ],
        'other' => [
            'salt' => 'salt',
            'length' => 0,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],
        'bilibili' => [
            // 此模式无需添加其他的配置
            // 前缀超过2位英文字母忽略
            'prefix' => '', // B站BV模式前缀类似: BV1fx411v7eo = 12345678
        ],
    ],
];
