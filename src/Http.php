<?php
/*
 * @LastEditors: zhlix <15127441165@163.com>
 * @LastEditTime: 2020-11-16 15:52:02
 * @FilePath: /think-helper/src/Http.php
 */

namespace zhlix\helper;

use Exception;

class Http
{
    /**
     * 初始化
     */
    public function __construct()
    {
        $this->_curl = curl_init();
        curl_setopt($this->_curl, CURLOPT_HEADER, false);
        curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    /**
     * curl 对象
     *
     * @var curl
     */
    protected $_curl = null;

    /**
     * 请求地址
     *
     * @var string
     */
    protected $_url = null;

    /**
     * 请求方式
     *
     * @var string
     */
    protected $_method = 'get';

    /**
     * 请求头信息
     *
     * @var array
     */
    protected $_header = [
        'content-type: application/json'
    ];

    /**
     * 请求参数
     *
     * @var array
     */
    protected $_params = [];

    /**
     * 超时时间
     *
     * @var integer
     */
    protected $_timeout = 60;

    /**
     * 连接超时时间
     *
     * @var integer
     */
    protected $_connect_timeout = null;

    /**
     * 数组转xml内容
     *
     * @param array $data
     *
     * @return null|string|string
     */
    protected function arr2json($data)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function ($matches) {
            return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");
        }, ($jsonData = json_encode($data)) == '[]' ? '{}' : $jsonData);
    }

    /**
     * 解析JSON内容到数组
     *
     * @param string $json
     *
     * @return array
     * @throws Exception
     */
    protected function json2arr($json)
    {
        $result = json_decode($json, true);

        if (is_array($result)) {
            if (empty($result)) {
                throw new Exception('invalid response.', '0');
            }
            if (!empty($result['errcode'])) {
                throw new Exception($result['errmsg'], $result['errcode']);
            }
        } else if (is_null($result) && !is_null($json)) {
            $result = $json;
        }
        return $result;
    }

    /**
     * 设置请求头信息
     *
     * @param array $header
     * @return zhlix\helper\Http
     */
    public function header(array $header)
    {
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array_merge($this->_header, $header));
        return $this;
    }

    /**
     * 设置请求地址
     *
     * @param string $url
     * @return zhlix\helper\Http
     */
    public function url(string $url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * 设置请求类型
     *
     * @param string $method
     * @return zhlix\helper\Http
     */
    public function method(string $method)
    {
        $this->_method = strtolower($method);
        return $this;
    }


    /**
     * 设置请求参数
     *
     * @param string $params
     * @return zhlix\helper\Http
     */
    public function params(array $params)
    {
        $this->_params = array_merge($this->_params, $params);
        return $this;
    }


    /**
     * 设置 auth 认证
     *
     * @param string $params
     * @return zhlix\helper\Http
     */
    public function auth(string $username, string $password = '')
    {
        if ($username && $password) {
            curl_setopt($this->_curl, CURLOPT_USERPWD, "$username:$password");
        }
        return $this;
    }

    /**
     * 设置超时时间
     *
     * @param integer $timeout
     * @param integer $connect_timeout
     * @return zhlix\helper\Http
     */
    public function timeout(int $timeout, int $connect_timeout = null)
    {
        $this->_timeout = $timeout;
        if ($connect_timeout) $this->_connect_timeout = $connect_timeout;
        return $this;
    }


    /**
     * 设置请求参数
     *
     * @param string $params
     * @return string
     */
    protected function setParams()
    {
        switch ($this->_method) {
            case 'get':
                return $this->_url . (stripos($this->_url, '?') !== false ? '&' : '?') . http_build_query($this->_params);
            default:
                curl_setopt($this->_curl, CURLOPT_POST, true);
                curl_setopt($this->_curl, CURLOPT_POSTFIELDS, json_encode($this->_params));
        }
    }

    protected function setTimeout()
    {
        curl_setopt($this->_curl, CURLOPT_TIMEOUT, $this->_timeout);
        if ($this->_connect_timeout ?? false) {
            curl_setopt($this->_curl, CURLOPT_CONNECTTIMEOUT, $this->_connect_timeout);
        }
    }

    public function get(string $url, array $data = [], array $header = [], string $auth = '')
    {
        return $this->request($url, 'get', $data, $header, $auth);
    }

    public function post(string $url, array $data = [], array $header = [], string $auth = '')
    {
        return $this->request($url, 'post', $data, $header, $auth);
    }

    public function request(string $url, string $method, array $data = [], array $header = [], string $auth = '')
    {
        return $this->url($url)
            ->method($method)
            ->params($data)
            ->header($header)
            ->auth(...explode(':', $auth))->do();
    }

    public function do()
    {
        validate_check([
            'url' => $this->_url
        ], [
            'url' => 'require'
        ], [
            'url' => 'url 参数不存在'
        ]);

        if (!($url = $this->setParams())) {
            $url = $this->_url;
        }
        $this->setTimeout();
        curl_setopt($this->_curl, CURLOPT_URL, $url);

        list($content) = [curl_exec($this->_curl), curl_close($this->_curl)];

        return $content;
    }
}
