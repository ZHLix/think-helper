<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-11-16 07:24:37
 * @FilePath: /app/src/facade/Http.php
 */


namespace zhlix\helper\facade;


use think\Facade;
use zhlix\helper\Http as HelperHttp;

/**
 * @see \zhlix\helper\Http
 * @package think\facade
 * @mixin \zhlix\helper\Http
 * @method static \zhlix\helper\Http header(array $header) 设置请求头
 * @method static \zhlix\helper\Http url(string $url) 设置请求地址
 * @method static \zhlix\helper\Http method(string $method) 设置请求类型
 * @method static \zhlix\helper\Http params(array $params) 设置请求参数
 * @method static \zhlix\helper\Http auth(string $username, string $password) 设置请求 auth 认证
 * @method static \zhlix\helper\Http timeout(string $timeout, string $connect_timeout) 设置超时时间
 * @method static mixed do() 执行函数
 * @method static mixed get(string $url, array $data, array $header, string $auth) get 请求
 * @method static mixed post(string $url, array $data, array $header, string $auth) post 请求
 * @method static mixed request(string $url, string $method, array $data, array $header, string $auth) 自定义请求
 */
class Http extends Facade
{
    protected static function getFacadeClass()
    {
        return HelperHttp::class;
    }
}
