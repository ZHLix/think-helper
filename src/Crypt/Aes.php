<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-16 21:14:32
 * @LastEditTime: 2021-05-19 11:54:37
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/Crypt/Aes.php
 */

namespace zhlix\helper\Crypt;

use phpseclib\Crypt\AES as CryptAES;
use zhlix\helper\Common\TempConfigFile;

class Aes
{
    /**
     * @return CryptAES
     */
    protected $aes;

    /**
     * @return Aes
     */
    protected static $instance;

    /**
     * @return array
     */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $default = [
            'path' => runtime_path() . 'crypt',
            'key' => null,
        ];

        $this->config = array_merge($default, $config);

        $this->initialize();
    }

    protected function initialize()
    {
        $this->aes = new CryptAES();
        if (!$this->config['key']) $this->initializeKey();
        $this->aes->setKey($this->config['key']);
    }

    public static function instance(): Aes
    {
        if (!self::$instance) self::$instance = new Aes();
        return self::$instance;
    }

    protected function initializeKey()
    {
        $tmp_path = "{$this->config['path']}/aes";
        if (!file_exists($tmp_path)) {
            $this->config['key'] = random_bytes(32);
            $temp = new TempConfigFile($tmp_path);
            $temp->empty()->add($this->config['key'])->save();
        } else {
            $this->config['key'] = file_get_contents($tmp_path);
        }
    }

    public function encrypt($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $result = $this->aes->encrypt($data);
        return base64_encode($result);
    }

    public function decrypt($data)
    {
        $data = base64_decode($data);
        $result = $this->aes->decrypt($data);
        return json_decode($result, JSON_OBJECT_AS_ARRAY);
    }
}
