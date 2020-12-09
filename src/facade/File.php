<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-12-09 15:53:21
 * @FilePath: /think-helper/src/facade/File.php
 */

namespace zhlix\helper\facade;

use think\Facade;
use zhlix\helper\File as HelperFile;

/**
 * @see \zhlix\helper\File
 * @package think\facade
 * @mixin \zhlix\helper\File
 * @method static HelperFile encrypt($bool = true) 是否加密文件路径
 * @method static HelperFile read(string $path) 读取服务器文件
 * @method static HelperFile upload() 读取上传列表
 */
class File extends Facade
{
    protected static function getFacadeClass()
    {
        return HelperFile::class;
    }
}
