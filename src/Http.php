<?php

namespace zhlix\helper;

use Exception;

class Http
{

    /**
     * 服务器域名
     */
    public function urlHeader ()
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        return $http_type . $_SERVER['HTTP_HOST'];
    }

    /**
     * 数组转xml内容
     *
     * @param array $data
     *
     * @return null|string|string
     */
    protected function arr2json ($data)
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
    protected function json2arr ($json)
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
     * 以get访问模拟访问
     *
     * @param string $url   访问URL
     * @param array  $query GET数
     * @param array  $options
     *
     * @return array
     * @throws Exception
     */
    public function get ($url, $query = [], $options = [])
    {
        $options['query'] = $query;
        return $this->json2arr($this->doRequest('get', $url, $options));
    }

    /**
     * 以post访问模拟访问
     *
     * @param string $url  访问URL
     * @param array  $data POST数据
     * @param array  $options
     *
     * @return array
     * @throws Exception
     */
    public function post ($url, $data = [], $options = [])
    {
        $options['data'] = $data;
        return $this->json2arr($this->doRequest('post', $url, $options));
    }

    /**
     * CURL模拟网络请求
     *
     * @param       $method
     * @param       $url
     * @param array $options
     *
     * @return mixed
     */
    protected function doRequest ($method, $url, $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= (stripos($url, '?') !== false ? '&' : '?') . http_build_query($options['query']);
        }
        // POST数据设置
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options['data']));
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            //            p($options['headers']);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        // auth 认证
        if (!empty($options['auth'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $options['auth']['username'] . ":" . $options['auth']['password']);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        list($content) = [curl_exec($curl), curl_close($curl)];

        return $content;
    }
}
