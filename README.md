# webman-hashids
- Webman 中使用 Hashids 用于将数字ID生成类似YouTube的ID。当您不想向用户公开数据库数字ID时使用
- 支持B站的ID生成模式，生成B站/video/`BV1fx411v7eo`这种ID

<p>
    <a href="https://packagist.org/packages/isszz/webman-hashids"><img src="https://img.shields.io/badge/php->=8.0-8892BF.svg" alt="Minimum PHP Version"></a>
    <a href="https://packagist.org/packages/isszz/webman-hashids"><img src="https://img.shields.io/badge/Webman->=1.4.x-8892BF.svg" alt="Minimum Webman Version"></a>
    <a href="https://packagist.org/packages/isszz/webman-hashids"><img src="https://poser.pugx.org/isszz/webman-hashids/v/stable" alt="Stable Version"></a>
    <a href="https://packagist.org/packages/isszz/webman-hashids"><img src="https://poser.pugx.org/isszz/webman-hashids/downloads" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/isszz/webman-hashids"><img src="https://poser.pugx.org/isszz/webman-hashids/license" alt="License"></a>
</p>

## 安装

```shell
composer require isszz/webman-hashids
```

## 配置

在 config/plugin/isszz/webman-hashids/app.php 中更改

```php
return [
    'enable'  => true,

    // 默认连接名称
    'default' => 'main', // 支持bilibili的BV模式

    // Hashids modes
    'modes' => [
        'main' => [
            'salt' => '',
            'length' => 0,
            'alphabet' => 'oqyei4pYnjDLXuPOw6c9IvzlWUmBs1Z0rdAkFCKM8hgHb2QV7NJ35TfaxRtESGArray'
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

```

## 用法
依赖注入方式-推荐

```php
use isszz\hashids\Hashids;

class Index
{
    public function index(Hashids $hashids)
    {
        // B站BV模式, B站模式支持第二个参数增加前缀，可以设置例如: prefix = 'BV'
        $_hashids = $hashids->mode(name: 'bilibili');
        $_hashids->encode(12345678); // 1fx411v7eo
        $_hashids->decode('1fx411v7eo'); // 12345678

        // other模式
        $hashids->mode('other')->encode(12345678); // gpyAoR
        $hashids->mode('other')->decode('gpyAoR'); // 12345678

        // 默认
        $hashids->encode(12345678); // 1rQ2go
        $hashids->decode('1rQ2go'); // 12345678

        // 其他传输ID的方式，返回为数组，对应传参
        $hashID = $hashids->encode(12, 34, 56, 78); // nyILSjosbR
        $hashID2 = $hashids->encode([12, 34, 56, 78]); // nyILSjosbR
        
        $result = $hashids->decode($hashID);
        // 返回数组
        /*
        $result = [
            '0' => 12
            '1' => 34
            '2' => 56
            '3' => 78
        ];
        */ 
    }
}


```

facade方式引入

```php
use isszz\hashids\facade\Hashids;

class Index
{
    public function index()
    {
        // B站BV模式
        Hashids::mode('bilibili')->encode(12345678); // 1fx411v7eo
        Hashids::mode('bilibili')->decode('1fx411v7eo'); // 12345678

        // other模式
        Hashids::mode('other')->encode(12345678); // gpyAoR
        Hashids::mode('other')->decode('gpyAoR'); // 12345678

        // 默认
        Hashids::encode(12345678); // 1rQ2go
        Hashids::decode('1rQ2go'); // 12345678
    }
}


```
助手函数
```php
class Index
{
    public function index()
    {
        // 加密
        id_encode(12345678); // 1rQ2go
        id_encode(12, 34, 56, 78, 'other'); // nyILSjosbR
        id_encode([12, 34, 56, 78], mode: 'other'); // nyILSjosbR

        // 解密
        id_decode('1rQ2go'); // 12345678
        id_decode('gpyAoR', 'other'); // 12345678

        // 切换模式
        id_mode('other')->encode(12345678); // gpyAoR
        id_mode('other')->decode('gpyAoR'); // 12345678

        // 助手函数还有一个获取字母表的函数
        // 拿到可以用来设置`config/plugin/isszz/webman-hashids/app.php `配置中的alphabet字段
        $alphabet = id_build_alphabet();
    }
}

```

- 基础库来自: [vinkla/hashids](https://github.com/vinkla/hashids)