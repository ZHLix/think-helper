<?php


namespace zhlix\helper\base;


trait Base
{
    /**
     * @var Base 对象实例
     */
    protected static $instance;

    /**
     * @return Base 实例化当前类
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}