<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-19 11:44:40
 * @LastEditTime: 2021-05-19 11:54:17
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/facade/Http.php
 */

namespace zhlix\helper\facade;

use think\Facade;
use zhlix\helper\Http as HelperHttp;


/**
 * @see Http
 * @package think/Facade
 * @method static string get(string $url, $data, $options) get 请求
 * @method static string post(string $url, $data, $options) post 请求
 * @method static string put(string $url, $data, $options) put 请求
 * @method static string delete(string $url, $data, $options) delete 请求
 *
 * @method static Http url(string $url) 设置 请求地址
 * @method static Http method(string $method) 设置 请求类型
 * @method static Http data($data) 设置 请求参数
 * @method static Http auth($auth) 设置 请求验证
 * @method static Http headers($headers) 设置 请求头
 * @method static Http options($options) 设置 请求选项
 */
class Http extends Facade
{
    protected static function getFacadeClass()
    {
        return HelperHttp::class;
    }
}
