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
            // 可配置前缀为: ['B', 'V']或者'BV'，超过2位忽略
            'prefix' => ['', ''], // B站BV模式前缀类似: BV1fx411v7eo = 12345678
        ],
    ],
];
