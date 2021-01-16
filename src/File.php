<?php
/*
 * @Author: zhlix
 * @Date: 2020-12-17 17:36:36
 * @LastEditTime: 2021-01-16 19:23:23
 * @LastEditors: zhlix <15127441165@163.com>
 * @FilePath: /think-helper/src/File.php
 */

namespace zhlix\helper;

use Exception;
use think\facade\Filesystem;

class File
{
    protected $config = [
        'upload_base_dir' => 'files', // 上传基础路径
        'iv' => null
    ];

    /**
     * 操作类型
     *
     * @var string
     */
    protected $type = null;

    /**
     * 上传路径是否加密
     *
     * @var boolean
     */
    protected $encrypt = false;

    /**
     * 读取服务器文件
     *
     * @var string
     */
    protected $read = null;

    /**
     * 上传文件列表
     *
     * @var array
     */
    protected $upload = [];

    /**
     * 初始化
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('zhlix_file'), $config);
    }

    /**
     * 输出地址是否加密
     *
     * @param boolean $bool
     * @return File
     */
    public function encrypt($bool = true)
    {
        $this->encrypt = $bool;
        return $this;
    }

    /**
     * 读取服务器文件
     *
     * @param string $path
     * @return File
     */
    public function read($path)
    {
        $this->upload = [];
        $this->type = 'read';

        if ($this->encrypt) $path = $this->decode($path);
        $filename = app()->getRootPath() . "runtime/storage/$path";

        $this->read = $filename;

        return $this;
    }

    public function info($name = null)
    {
        if ($this->type == 'read') {
            switch ($name) {
                case 'name':
                    return basename($this->read);
                case 'path':
                    return $this->read;
                case 'size':
                    return filesize($this->read);
                case 'mime':
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    return finfo_file($finfo, $this->read);
                case 'mtime':
                    return filemtime($this->read);
                default:
                    return [
                        'size' => filesize($this->read),
                        'mime' => mime_content_type($this->read),
                        'mtime' => filemtime($this->read),
                    ];
            }
        }
    }

    /**
     * 读取上传列表
     *
     * @return File
     */
    public function upload($files = [])
    {
        if (empty($files)) $files = request()->file();
        $this->read = null;
        $this->type = 'upload';

        $this->upload = $files;

        return $this;
    }

    /**
     * 上传文件
     */
    public function save($filenames = [])
    {
        if (empty($this->upload)) throw new Exception('请选择上传文件');
        $result = [];
        foreach ($this->upload as $k => $v) {
            if (!empty($filenames)) {
                $path = Filesystem::putFileAs($this->config['upload_base_dir'], $v, $filenames[$k]);
            } else {
                $path = Filesystem::putFile($this->config['upload_base_dir'], $v);
            }
            if ($this->encrypt) $path = $this->encode($path);
            $result[$k] = $path;
        }
        return $result;
    }

    /**
     * 浏览器输出
     *
     * @return void
     */
    public function output($filename = null)
    {
        if ($this->type == 'read') {
            if (empty($this->read)) throw new Exception('文件错误');
            $content = file_get_contents($this->read);
            if (empty($filename)) $filename = $this->info('name');
            $header = [
                'content-type' => $this->info('mime'),
                "Accept-Length" =>  $this->info('size'),
                'Content-Disposition' => "attachment; filename=" . $filename
            ];
            return response($content, 200, $header);
        }
    }

    public function delete($path = null)
    {
        if (!$path) $path = $this->read;
        if (!file_exists($path)) throw new Exception('目标文件不存在');
        unlink($path);
    }

    public function encode($path)
    {
        return str_replace('//', '_%_', aes_encode(str_replace("{$this->config['upload_base_dir']}/", '', $path), $this->config['iv']));
    }

    public function decode($path)
    {
        return "{$this->config['upload_base_dir']}/" . aes_decode(str_replace('_%_', '//', $path));
    }
}
