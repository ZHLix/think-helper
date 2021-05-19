<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-18 13:16:20
 * @LastEditTime: 2021-05-19 11:54:52
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/Common/TempConfigFile.php
 */

namespace zhlix\helper\Common;

class TempConfigFile
{
    /**
     * @return string
     */
    protected $path;

    /**
     * @return string
     */
    protected $content;

    /**
     * @return string
     */
    protected $origin_content;

    /**
     * @return string
     */
    protected $dir;

    public function __construct($path)
    {
        $this->path = $path;
        $this->dir = str_replace(basename($path), '', $path);

        $this->initialize();
    }

    protected function initialize()
    {
        if (file_exists($this->path)) {
            $this->content = $this->origin_content = file_get_contents($this->path);
        } else {
            $this->empty();
        }
    }

    public function add(string $value): TempConfigFile
    {
        $this->content .= $value;
        return $this;
    }

    public function empty(): TempConfigFile
    {
        $this->content = '';
        return $this;
    }

    public function save()
    {
        if ($this->origin_content == $this->content) return;
        if (!file_exists($this->dir)) mkdir($this->dir, 0777, true);
        file_put_contents($this->path, $this->content);
        $this->saved = true;
    }

    public function __toString()
    {
        return $this->content;
    }

    // public function __destruct()
    // {
    //     if ($this->content) {
    //         $this->save();
    //     }
    // }
}
