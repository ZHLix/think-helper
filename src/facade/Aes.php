<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-18 14:15:50
 * @LastEditTime: 2021-05-19 11:54:03
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/facade/Aes.php
 */

namespace zhlix\helper\facade;

use think\Facade;
use zhlix\helper\Crypt\Aes as CryptAes;

/**
 * @see CryptAes
 * @package think/Facade
 * @method static string encrypt(mixed $data) aes 加密
 * @method static mixed decrypt(string $data) aes 解密
 */
class Aes extends Facade
{
    protected static function getFacadeClass()
    {
        return CryptAes::class;
    }
}
