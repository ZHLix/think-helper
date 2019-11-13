<?php


namespace zhlix\helper\encrypt;


class Rsa
{
    /**
     * @var Rsa 对象实例
     */
    protected static $instance;

    /**
     * @var array 初始配置项
     */
    protected $config = [
        /**
         * @var string 服务器类型 WINNT Linux
         */
        'serverType' => PHP_OS,
        /**
         * @var string window 系统下 openssl 路径
         */
        'opensslCnf' => 'C:\phpstudy_pro\Extensions\Apache2.4.39\conf\openssl.cnf',
        /**
         * @var string 密钥对存放文件夹名
         */
        'folderName' => 'rsa_cache'
    ];

    /**
     * @var string 加密的 密钥对存放文件夹名
     */
    protected $folderName = null;

    /**
     * @var string 私钥
     */
    public $privateKey = null;

    /**
     * @var string 公钥
     */
    public $publicKey = null;

    /**
     * @param array $config
     * @return Rsa 实例化当前类
     */
    public static function instance($config = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * Rsa constructor. 初始化
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, config('rsa') ?: []);
        if (isset($config['opensslCnf'])) $this->config['opensslCnf'] = $config['opensslCnf'];
        if (isset($config['folderName'])) $this->config['folderName'] = $config['folderName'];

        $this->folderName = md5($this->config['folderName']);
        if (!is_dir($this->folderName)) mkdir(md5($this->config['folderName']));

        if (!file_exists("{$this->folderName}/private_key.pem") || !file_exists("{$this->folderName}/public_key.pem")) {
            $this->generator();
        }

        if (is_null($this->privateKey)) $this->privateKey = file_get_contents("{$this->folderName}/private_key.pem");
        if (is_null($this->publicKey)) $this->publicKey = file_get_contents("{$this->folderName}/public_key.pem");
    }

    /**
     * 初始化 密钥对
     * @return $this
     */
    public function generator()
    {
        $config = [
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,           //字节数  512 1024 2048  4096 等 ,不能加引号，此处长度与加密的字符串长度有关系，可以自己测试一下
            "private_key_type" => OPENSSL_KEYTYPE_RSA,   //加密类型
        ];
        if ($this->config['serverType'] === 'WINNT') $config['config'] = $this->config['opensslCnf'];
        $rsa = openssl_pkey_new($config);
        // 提取私钥
        openssl_pkey_export($rsa, $private_key, null, $config);
        // 生成公钥
        $public_key = openssl_pkey_get_details($rsa)['key'];

        $this->privateKey = $private_key;
        file_put_contents("{$this->folderName}/private_key.pem", $private_key);
        $this->publicKey = $public_key;
        file_put_contents("{$this->folderName}/public_key.pem", $public_key);

        return $this;
    }

    /**
     * 私钥解密
     *
     * @param string $data
     *
     * @return string
     */
    public function decode($data)
    {
        openssl_private_decrypt(base64_decode($data), $output, $this->privateKey);
        return $output;
    }

    /**
     * 公钥加密
     *
     * @param string $data
     *
     * @return string
     */
    public function encode($data)
    {
        openssl_public_encrypt($data, $output, $this->publicKey);
        return base64_encode($output);
    }
}
