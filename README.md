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

- 查看更多用法: [vinkla/hashids](https://github.com/vinkla/hashids)