<?php
/*
 * @Author: zhlix
 * @Date: 2020-12-17 17:36:36
 * @LastEditTime: 2021-01-11 18:23:13
 * @LastEditors: zhlix <15127441165@163.com>
 * @FilePath: /think-helper/src/BaseModel.php
 */


namespace zhlix\helper;


use think\Model;
use think\model\concern\SoftDelete;

class BaseModel extends Model
{
    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';

    protected $hidden = ['update_time', 'delete_time'];
}