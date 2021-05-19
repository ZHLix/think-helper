<?php
/*
 * @Author: zhlix
 * @Date: 2020-12-17 17:36:36
 * @LastEditTime: 2021-05-19 12:28:16
 * @LastEditors: zhlix <2689921152@qq.com>
 * @FilePath: /think-helper/helper.php
 */

use think\facade\Db;
use zhlix\helper\facade\Aes;
use zhlix\helper\facade\Http;
use zhlix\helper\facade\Rsa;

if (!function_exists('result')) {
    /**
     * api 请求返回结果函数
     */
    function result($data, $code = 0, $msg = null)
    {
        if ($code != 0) return json(compact('code', 'msg'), 200);
        return json(compact('code', 'data'), 200);
    }
}

if (!function_exists('nonce_str')) {
    /**
     * 创建随机字段
     *
     * @param integer $length
     * @param string  $str
     * @param [type] $chars
     *
     * @return string
     */
    function nonce_str($length = 32, $str = "", $chars = null)
    {
        if (is_null($chars) || $chars == '') {
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
        }
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}

if (!function_exists('aes_encode')) {
    /**
     * aes 加密
     *
     * @param mixed $data
     * @return string
     */
    function aes_encode($data, $iv = null): string
    {
        return Aes::encrypt($data, $iv);
    }
}

if (!function_exists('aes_decode')) {
    /**
     * aes 解密
     *
     * @param string $data
     * @return mixed
     */
    function aes_decode($data)
    {
        return Aes::decrypt($data);
    }
}

if (!function_exists('rsa_encode')) {
    /**
     * rsa 公钥加密
     *
     * @param $data
     *
     * @return string
     */
    function rsa_encode($data)
    {
        return Rsa::encrypt($data, 'public');
    }
}

if (!function_exists('rsa_decode')) {
    /**
     * rsa 私钥解密
     *
     * @param $string
     *
     * @return string
     */
    function rsa_decode($string)
    {
        return Rsa::decrypt($string, 'private');
    }
}


if (!function_exists('trans')) {
    function trans(Closure $closure)
    {
        // 开启事务
        Db::startTrans();
        try {
            $result = $closure();
            Db::commit();
            return $result;
        } catch (Exception $e) {
            Db::rollback();
            return ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('http')) {
    /**
     * 以get访问模拟访问
     *
     * @param string $url   访问URL
     * @param array  $params GET数
     * @param array  $header
     *
     * @return array
     * @throws Exception
     */
    function http($url, $method = 'get', $data = null, $options = null)
    {
        return Http::url($url)->method($method)->data($data)->options($options)->send();
    }
}
