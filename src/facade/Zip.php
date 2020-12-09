<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-12-09 19:49:57
 * @FilePath: /think-helper/src/facade/Zip.php
 */

namespace zhlix\helper\facade;

use think\Facade;
use zhlix\helper\Zip as HelperZip;

/**
 * @see \zhlix\helper\HelperZip
 * @package think\facade
 * @mixin \zhlix\helper\HelperZip
 *
 * @method static HelperZip read() 读取压缩文件
 */
class Zip extends Facade
{
    protected static function getFacadeClass()
    {
        return HelperZip::class;
    }
}