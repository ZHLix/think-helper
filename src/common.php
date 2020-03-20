<?php

use think\facade\Db;
use \think\response\Json;
use zhlix\helper\curl\Http;
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

if (!function_exists('http_get')) {
    /**
     * 以get访问模拟访问
     *
     * @param string $url 访问URL
     * @param array $query GET数
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    function http_get($url, $query = [], $options = [])
    {
        return Http::get($url, $query, $options);
    }
}

if (!function_exists('http_post')) {
    /**
     * 以post访问模拟访问
     *
     * @param string $url 访问URL
     * @param array $data POST数据
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    function http_post($url, $data = [], $options = [])
    {
        return Http::post($url, $data, $options);
    }
}

if (!function_exists('handle')) {
    /**
     * @return Json
     */
    function handle(Closure $closure, $trans = false)
    {
        // 开启事务
        $trans && Db::startTrans();
        try {
            $res = $closure();
            // 提交事务
            $trans && Db::commit();
            return $res;
        } catch (Exception $e) {
            // 回滚事务
            $trans && Db::rollback();
            return result(null, $e->getCode() ?: 400, $e->getMessage());
        }
    }
}

if (!function_exists('captcha_img_re')) {
    /**
     * 验证码 添加刷新功能
     *
     * @param bool $isLogin
     *
     * @return string
     */
    function captcha_img_re($isLogin = false)
    {
        $height = 38;
        if ($isLogin) $height = 50;
        return '<img class="captcha" src="' . captcha_src() . '" onclick="this.src=\'' . captcha_src() . '\'" title=\'看不清？点击换一张\' style="height: ' . $height . 'px">';
    }
}

// 应用公共文件
if (!function_exists('asset')) {
    /**
     * 页面资源模板路径
     *
     * @param $path
     *
     * @return string
     */
    function asset($path)
    {
        $path = "assets/$path";
        preg_match("/\.(.*)$/", $path, $output);
        list(, $ext) = $output;

        if ($ext === 'js' && file_exists("./$path")) {
            return "<script src='/$path'></script>";
        } else if ($ext === 'css' && file_exists("./$path")) {
            return "<link rel='stylesheet' href='/$path'>";
        } else {
            exit("$path 文件不存在或文件并不是 css/js 资源文件");
        }
    }
}
