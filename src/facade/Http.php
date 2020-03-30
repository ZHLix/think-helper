<?php


namespace zhlix\helper\facade;


use think\Facade;

class Http extends Facade
{
    protected static function getFacadeClass ()
    {
        return 'zhlix\\helper\\Http';
    }
}