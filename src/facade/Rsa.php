<?php


namespace zhlix\helper\facade;


use think\Facade;

class Rsa extends Facade
{
    protected static function getFacadeClass ()
    {
        return 'zhlix\\helper\\Rsa';
    }
}