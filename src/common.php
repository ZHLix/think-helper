<?php


use \think\response\Json;
use zhlix\helper\encrypt\Rsa;

if (!function_exists('p')) {
    /**
     * 程序调试函数
     *
     * @param      $data
     * @param bool $dump
     * @param bool $die
     */
    function p($data, $dump = false, $die = true)
    {
        if ($dump) {
            dump($data);
        } else {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }
        $die && die();
    }
}

if (!function_exists('result')) {
    /**
     * 输出数据
     *
     * @param null $data
     * @param int $code
     * @param string $msg
     *
     * @return Json
     */
    function result($data = null, $code = 200, $msg = '')
    {
        $res = [
            'code' => $code,
            'data' => empty($data) ? null : $data,
            'timestamp' => time(),
            'msg' => $msg
        ];

        return json($res, 200);
    }
}

if (!function_exists('time_micro')) {
    /**
     * @return float 当前时间（加上毫秒）
     */
    function time_micro()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}

if (!function_exists('rand_str')) {
    /**
     * 生成随机数
     * @param int $length
     * @param string $string
     * @return string
     */
    function rand_str($length = 4, $string = '')
    {
        if (!$string) $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $stringLength = strlen($string) - 1;
        $randString = '';
        for ($i = 0; $i < $length; $i++) {
            $number = mt_rand(0, $stringLength);
            $randString .= $string[$number];
        }
        return $randString;
    }
}

if (!function_exists('rsa_decode')) {
    /**
     * rsa 私钥解密
     * @param $string
     * @return string
     */
    function rsa_decode($string)
    {
        return Rsa::instance()->decode($string);
    }
}

if (!function_exists('rsa_encode')) {
    /**
     * rsa 公钥加密
     * @param $string
     * @return string
     */
    function rsa_encode($string)
    {
        return Rsa::instance()->encode($string);
    }
}
