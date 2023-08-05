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

**依赖注入方式**
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

**facade方式引入**

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
**助手函数**
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
**使用ThinkORM获取器对ID进行加密**
```php
public function getIdAttr($value)
{
    return id_encode($value);
}

// 主键非id时, 比如是tid时
public function getTidAttr($value)
{
    return id_encode($value);
}

```
**使用Laravel Eloquent ORM访问器对ID进行加密**
```php
// 10.x版本
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => id_encode($value),
        );
    }
}

// 8.x版本
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function getIdAttribute($value)
    {
        return id_encode($value);
    }
}

```
### Request请求中的使用案例

**新建一个路由中间件，在需要的路由引入，不需要解密的路由不建议引入**

```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Hashid implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $parameters = [];
        $route = $request->route;

        if ($route) {
            $parameters = $route->param() ?: [];

            foreach ($parameters as $k => $v) {
                $parameters[$k] = $this->decodeParam($v) ?: $v;
            }

            $route->setParams($parameters);
        }

        // POST + GET 用data传值，因官方没有对参数进行二次修改的方法只有这样啦，不给也挺好用的
        $parameters = $request->all();
        if ($parameters && count($parameters) > 0) {
            foreach ($parameters as $k => $v) {
                $parameters[$k] = $this->decodeParam($v) ?: $v;
            }

            $request->data = $parameters;
        }

        return $next($request);
    }


    private function decodeParam($value)
    {
        if (!preg_match("/^[0-9a-zA-Z@]+$/", $value)) {
            return null;
        }

        // 切换模式
        if (str_contains($value, '@')) {
            [$value, $type] = explode('@', $value);
        }

        try {
            return id_decode($value, $type ?? '') ?: null;
        } catch(\Exception $e) {}

        return null;
    }
}


```
**修改`support\Request.php`增加如下方法**
```php
class Request extends \Webman\Http\Request
{
    /**
     * 获取中间件中，以data参数传递的get或者post参数，通常此方法获取的是经过中间件处理后的参数
     * 
     * @param string|array|null $name
     * @param mixed $default
     * @return mixed|null
     */
    public function data(string|array|null $name = null, $default = null)
    {
        $data = $this->data ?: [];

        if(is_null($name)) {
            return $data;
        }

        $result = [];
        if(is_array($name) && count($data) > 0) {
            foreach ($name as $key => $val) {

                if (is_int($key)) {
                    $default = null;
                    $key = $val;
                    if (!key_exists($key, $data)) {
                        continue;
                    }
                } else {
                    $default = $val;
                }

                $result[$key] = $data[$key] ?? $default;
            }

            return $result;
        }

        return $data[$name] ?? $default;
    }
            
     

    /**
     * 从路由中获取参数
     * 
     * @param string|array|null $name
     * @param mixed $default
     * @return mixed|null
     */
    public function route(string|array|null $name = null, $default = null)
    {
        $data = $this->route->param() ?: [];

        if(is_null($name)) {
            return $data;
        }

        $result = [];
        if(is_array($name) && count($data) > 0) {
            foreach ($name as $key => $val) {

                if (is_int($key)) {
                    $default = null;
                    $key = $val;
                    if (!key_exists($key, $data)) {
                        continue;
                    }
                } else {
                    $default = $val;
                }

                $result[$key] = $data[$key] ?? $default;
            }

            return $result;
        }

        return $data[$name] ?? $default;
    }
}

```
**控制器中使用例如**
```php
class TestController
{
    public function index(Request $request)
    {
        // 正常的参数传递
        // /test?id=1rQ2go&uid=daVBjxW@other
        $request->data(['uid', 'id']);
        $request->data('id');
        $request->data('uid');

        // 使用路由定义的参数
        // /test/{id}/user/{uid}
        // 官方的方法可以这样一次一个参数拿
        $request->route->param('uid');

        // 我们有对request增加一些方法比如，这样可以批量拿
        [$uid, $id] = $request->route(['uid', 'id']);

        // 也可以
        $uid = $request->route('uid', null);

        // 其中参数中以@分割，前面是要解析的参数，后面的是以那种模式解析
        return 'end';

    }
}

```
- 基础库来自: [vinkla/hashids](https://github.com/vinkla/hashids)