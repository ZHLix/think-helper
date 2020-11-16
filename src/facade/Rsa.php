<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-11-16 07:19:14
 * @FilePath: /app/src/facade/Rsa.php
 */


namespace zhlix\helper\facade;


use think\Facade;
use zhlix\helper\Rsa as HelperRsa;

/**
 * @see \zhlix\helper\Rsa
 * @package think\facade
 * @mixin \zhlix\helper\Rsa
 * @method static string encode(string $data) rsa 加密
 * @method static string decode(string $data) rsa 解密
 * @method static string getPublicKey(string $data) 获取公钥
 * @method static string getPrivateKey(string $data) 获取私钥
 */
class Rsa extends Facade
{
    protected static function getFacadeClass ()
    {
        return HelperRsa::class;
    }
}