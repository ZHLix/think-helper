<?php

use think\Validate;
use zhlix\helper\facade\Aes;
use zhlix\helper\facade\Http;
use zhlix\helper\facade\Rsa;

if (!function_exists('validate_check')) {
    function validate_check($data, $rule, $msg, $extends = [])
    {
        $validate = new Validate;
        $validate->rule($rule);
        foreach ($extends as $k => $val) {
            $validate->extend($k, $val);
        }
        $validate->message($msg);
        return $validate->failException(true)->check($data);
    }
}

if (!function_exists('result')) {
    /**
     * 输出数据
     *
     * @param null   $data
     * @param int    $code
     * @param string $msg
     * @param array $options
     *
     * @return mixed
     */
    function result($data = null, $status = 1, $msg = '', $options = [])
    {
        $continue = function ($result) {
            ignore_user_abort();
            set_time_limit(0);
            ob_end_clean();
            ob_start();
            echo json_encode($result); //返回结果给ajax
            //-----------------------------------------------------------------------------------
            // get the size of the output
            $size = ob_get_length();
            // send headers to tell the browser to close the connection
            header("Content-Length: $size");
            header('Connection: close');
            header("HTTP/1.1 200 OK");
            header("Content-Type: application/json;charset=utf-8");
            ob_end_flush();
            if (ob_get_length())
                ob_flush();
            flush();
            if (function_exists("fastcgi_finish_request")) { // yii或yaf默认不会立即输出，加上此句即可（前提是用的fpm）
                fastcgi_finish_request();                    // 响应完成, 立即返回到前端,关闭连接
            }
        };
        $result = array_merge([
            'status' => $status,
            'msg' => $msg,
            'timestamp' => time()
        ], $options);
        if (!empty($data)) $result['data'] = $data;

        if ($options['continue'] ?? false) {
            return $continue($result);
        }
        return json($result, $options['code'] ?? 200);
    }
}


if (!function_exists('process_create')) {
    function process_create(\Closure $closure = null, $max = 10)
    {
        if (is_null($closure)) return null;

        /**
         * -1 创建子进程失败
         * 1 主进程
         * 0 子进程
         */
        $processId = pcntl_fork();

        if ($processId) { // 主进程
            $processTotal = (int)shell_exec('ps -ef|grep php|grep -v think|grep -v sh|grep -v WorkerMan|grep -v grep|grep -v defunct|wc -l') - 1;
            if ($processTotal >= $max) { // 控制进程数
                pcntl_wait($status);
            }
        } else {
            $closure();
            posix_kill(posix_getpid(), SIGKILL);
        }
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
        return Rsa::decode($string);
    }
}

if (!function_exists('rsa_encode')) {
    /**
     * rsa 公钥加密
     *
     * @param $string
     *
     * @return string
     */
    function rsa_encode($string)
    {
        return Rsa::encode($string);
    }
}

if (!function_exists('aes_encode')) {
    /**
     * aes 加密
     *
     * @param mixed $data
     * @return string
     */
    function aes_encode($data): string
    {
        return Aes::encode($data);
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
        return Aes::decode($data);
    }
}

if (!function_exists('http_get')) {
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
    function http_get($url, $params = [], $header = [], $auth = '')
    {
        return Http::get($url, $params, $header, $auth);
    }
}

if (!function_exists('http_post')) {
    /**
     * 以post访问模拟访问
     *
     * @param string $url  访问URL
     * @param array  $params POST数据
     * @param array  $header
     *
     * @return array
     * @throws Exception
     */
    function http_post($url, $params = [], $header = [], $auth = '')
    {
        return Http::post($url, $params, $header, $auth);
    }
}

if (!function_exists('listen')) {
    function listen(Closure $closure, $trans = false)
    {
        $debug = env('APP_DEBUG', '0') == '1';

        // 开启事务
        $trans && \think\facade\Db::startTrans();
        try {
            $result = $closure();
            $trans && \think\facade\Db::commit();
            return $result;
        } catch (Exception $e) {
            $trans && \think\facade\Db::rollback();
            if ($debug) return $e;
            return result(null, $e->getCode(), $e->getMessage());
        }
    }
}
