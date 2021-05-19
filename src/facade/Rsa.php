<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-18 14:15:50
 * @LastEditTime: 2021-05-19 12:29:57
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/facade/Rsa.php
 */

namespace zhlix\helper\facade;

use think\Facade;
use zhlix\helper\Crypt\Rsa as CryptRsa;

/**
 * @see CryptRsa
 * @package think/Facade
 * @method static string encrypt(mixed $data, $type = 'public') rsa 加密
 * @method static mixed decrypt(string $data, $type = 'private') rsa 解密
 */
class Rsa extends Facade
{
    protected static function getFacadeClass()
    {
        return CryptRsa::class;
    }
}
