<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-12-09 06:41:41
 * @FilePath: /app/src/facade/Aes.php
 */


namespace zhlix\helper\facade;


use think\Facade;
use zhlix\helper\Aes as HelperAes;

/**
 * @see \zhlix\helper\Aes
 * @package think\facade
 * @mixin \zhlix\helper\Aes
 * @method static mixed encode($data) Aes 加密
 * @method static mixed decode(string $data) Aes 解密
 */
class Aes extends Facade
{
    protected static function getFacadeClass ()
    {
        return HelperAes::class;
    }
}