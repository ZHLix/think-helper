<?php
/*
 * @Author: zhlix
 * @Date: 2021-05-19 11:11:47
 * @LastEditTime: 2021-05-19 12:24:51
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/src/Http.php
 */

namespace zhlix\helper;

use Exception;
use GuzzleHttp\Client;
use JsonException;

class Http
{
    /**
     * @return Client
     */
    protected $client;

    /**
     * @return string
     */
    protected $url;

    /**
     * @return string
     */
    protected $method;

    /**
     * @return mixed
     */
    protected $data;

    /**
     * @return mixed
     */
    protected $auth;

    /**
     * @return array
     */
    protected $headers;

    /**
     * @return array
     */
    protected $options = [];

    public function __construct(array $config = [])
    {
        $this->options = $config;
        $this->client = new Client($this->options);
    }

    public function url(string $url): Http
    {
        $this->url = $url;
        return $this;
    }

    public function method(string $method): Http
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function data($data): Http
    {
        if (!is_null($data)) $this->data = $data;
        return $this;
    }

    public function auth($auth): Http
    {
        if (!is_null($auth)) $this->auth = $auth;
        return $this;
    }

    public function headers($headers, $reset = false): Http
    {
        if (!is_null($headers)) {
            if ($reset) {
                $this->headers = $headers;
            } else {
                $this->headers = array_merge($this->headers, $headers);
            }
        }
        return $this;
    }


    public function options($options, $reset = false): Http
    {
        if (!is_null($options)) {

            if ($reset) {
                $this->options = $options;
            } else {
                $this->options = array_merge($this->options, $options);
            }
        }
        return $this;
    }

    public function send()
    {
        try {
            $result = (string) $this->client->request($this->method, $this->url, $this->parseOptions())->getBody();
            $tmp = json_decode($result);
            if (is_null($tmp)) throw new JsonException();
            return $tmp;
        } catch (JsonException $e) {
            return $result;
        }
    }

    protected function parseOptions(): array
    {
        $tmp = $this->options;
        if ($this->auth) $tmp['auth'] = $this->auth;
        if ($this->headers) $tmp['headers'] = $this->headers;
        if ($this->data) {
            ['key' => $key, 'value' => $value] = $this->parseData();
            $tmp[$key] = $value;
        }
        return $tmp;
    }

    protected function parseData(): array
    {
        $key = $this->method == 'GET' ? 'query' : 'params';
        return ['key' => $key, 'value' => $this->data];
    }

    protected function defaultSend($url, $data = null, $options = null)
    {
        $this->url($url);
        if ($data) $this->data($data);
        if ($options) $this->options($options);
        return $this->send();
    }

    public function get($url, $data = null, $options = null)
    {
        $this->method('get');
        return $this->defaultSend($url, $data, $options);
    }

    public function post($url, $data = null, $options = null)
    {
        $this->method('post');
        return $this->defaultSend($url, $data, $options);
    }

    public function put($url, $data = null, $options = null)
    {
        $this->method('put');
        return $this->defaultSend($url, $data, $options);
    }

    public function delete($url, $data = null, $options = null)
    {
        $this->method('delete');
        return $this->defaultSend($url, $data, $options);
    }
}
