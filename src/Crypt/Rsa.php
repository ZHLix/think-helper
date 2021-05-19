<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-16 21:13:38
 * @LastEditTime: 2021-05-19 11:54:44
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/Crypt/Rsa.php
 */

namespace zhlix\helper\Crypt;

use phpseclib\Crypt\RSA as CryptRSA;
use zhlix\helper\Common\TempConfigFile;

class Rsa
{

    /**
     * @return CryptRSA
     */
    protected $rsa;

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
            'private_key' => null,
            'public_key' => null,
        ];

        $this->config = array_merge($default, $config);

        $this->initialize();
    }

    protected function initialize()
    {
        $this->rsa = new CryptRSA();
        if (!$this->config['private_key']) $this->initializeKey();
    }

    public static function instance(): Rsa
    {
        if (!self::$instance) self::$instance = new Rsa();
        return self::$instance;
    }

    protected function initializeKey()
    {
        $tmp_path = "{$this->config['path']}/private_key";
        if (!file_exists($tmp_path)) {
            ['privatekey' => $private_key, 'publickey' => $public_key] = $this->rsa->createKey();

            foreach (compact('private_key', 'public_key') as $k => $v) {
                $this->config[$k] = $v;
                $tmp_path = "{$this->config['path']}/$k";
                $temp = new TempConfigFile($tmp_path);
                $temp->empty()->add($v)->save();
            }
        } else {
            $this->config['private_key'] = file_get_contents("{$this->config['path']}/private_key");
            $this->config['public_key'] = file_get_contents("{$this->config['path']}/public_key");
        }
    }

    public function encrypt($data, $type = 'private')
    {
        if (!in_array($type, ['private', 'public'])) return false;
        $this->rsa->loadKey($this->config["{$type}_key"]);

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $result = $this->rsa->encrypt($data);
        return base64_encode($result);
    }

    public function decrypt($data, $type = 'public')
    {
        if (!in_array($type, ['private', 'public'])) return false;
        $this->rsa->loadKey($this->config["{$type}_key"]);

        $data = base64_decode($data);
        $result = $this->rsa->decrypt($data);
        return json_decode($result, JSON_OBJECT_AS_ARRAY);
    }
}
