<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-12-18 11:13:18
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
        $tmp = base64_encode(openssl_encrypt(
            json_encode($data),
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
        $result = openssl_decrypt(
            base64_decode($data),
            'AES-128-CBC',
            $this->config['key'],
            $this->options(),
            $iv
        );
        $result_json = json_decode($result, 1);
        if ($result_json) return $result_json;
        return $result;
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
