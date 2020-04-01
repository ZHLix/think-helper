<?php


namespace zhlix\helper;


use think\Model;
use think\model\concern\SoftDelete;

class BaseModel extends Model
{
    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';

    protected $hidden = ['update_time', 'delete_time'];
}