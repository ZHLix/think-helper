<?php


namespace zhlix\helper\facade;


use think\Facade;

class Handle extends Facade
{
    protected static function getFacadeClass ()
    {
        return 'zhlix\\helper\\Handle';
    }
}