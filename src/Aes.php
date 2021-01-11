<?php
/*
 * @Author: zhlix
 * @Date: 2020-12-17 17:36:36
 * @LastEditTime: 2021-01-11 18:22:58
 * @LastEditors: zhlix <15127441165@163.com>
 * @FilePath: /think-helper/src/Aes.php
 */

namespace zhlix\helper;

class Aes
{
    protected $config = [
        'key' => 'is encrypt key.', // 加密秘钥
        'type' => 'php'
    ];

    /**
     * 初始化
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('aes'), $config);
    }

    protected function options()
    {
        return $this->config['type'] == 'php' ? 1 : OPENSSL_ZERO_PADDING;
    }

    /**
     * 加密
     *
     * @param [type] $data
     * @return string
     */
    public function encode($data, $iv = null)
    {
        if (!$iv) $iv = nonce_str(16);
        $data = json_encode($data);
        $tmp = base64_encode(openssl_encrypt(
            $data,
            'AES-128-CBC',
            $this->config['key'],
            $this->options(),
            $iv
        ));
        return $this->ivHandle($tmp, $iv);
    }

    /**
     * 解密
     *
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        [$data, $iv] = $this->ivHandle($data);
        return json_decode(openssl_decrypt(
            base64_decode($data),
            'AES-128-CBC',
            $this->config['key'],
            $this->options(),
            $iv
        ), 1);
    }

    protected function ivHandle(string $data, $iv = '')
    {
        if ($iv) {
            preg_match('/(.{5})(.*)/', $data, $result);
            return "{$result[1]}$iv{$result[2]}";
        } else {
            preg_match('/(.{5})(.{16})(.*)/', $data, $result);
            return ["{$result[1]}{$result[3]}", $result[2]];
        }
    }
}
