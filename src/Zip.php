<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-12-09 09:42:28
 * @FilePath: /app/vendor/zhlix/think-helper/src/Zip.php
 */

namespace zhlix\helper;

use ZipArchive;

class Zip
{
    /**
     * 设置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * 压缩文件路径
     *
     * @var string
     */
    protected $filename = null;

    protected $tmp_dir = '';

    /**
     * 初始化
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('zhlix_zip'), $config);
    }

    /**
     * 读取压缩文件
     *
     * @param string $filename
     * @return Zip
     */
    public function read(string $filename)
    {
        $this->filename = $filename;
        $this->tmp_dir = runtime_path() . md5($filename);
        $zip = new ZipArchive;
        if ($zip->open($filename) === true) {
            $zip->extractTo($this->tmp_dir);
            $zip->close();
        }
        return $this;
    }

    public function list($glob = '**/*.*')
    {
        $files = glob("{$this->tmp_dir}/$glob");
        return $files;
    }

    /**
     * 删除缓存目录
     */
    public function __destruct()
    {
        $this->delTree($this->tmp_dir);
    }

    protected function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
