<?php


namespace zhlix\helper;


use Exception;
use think\facade\Db;

class Handle
{
    /**
     * 主体执行函数
     *
     * @var \Closure
     */
    protected $func = null;

    /**
     * 是否开启事务
     *
     * @var bool
     */
    protected $trans = false;

    /**
     * @var mixed 数据
     */
    protected $data = null;

    /**
     * @var int 状态码
     */
    protected $code = 200;

    /**
     * @var string 说明
     */
    protected $message = '';

    /**
     * Handle constructor.
     *
     * @param \Closure $closure
     */
    public function __construct($closure = null)
    {
        if (!is_null($closure)) $this->func = $closure;
    }

    /**
     * @param \Closure $closure
     */
    public function exec($closure = null)
    {
        if ($closure) $this->func = $closure;

        // 开启事务
        $this->trans && Db::startTrans();
        try {
            $func = $this->func;
            $res  = $func();
            // 提交事务
            $this->trans && Db::commit();
            if (!is_array($res)) $res = [$res];
            switch (count($res)) {
                case 1:
                    $this->data = $res[0];
                    break;
                case 2:
                    $this->data    = $res[0];
                    $this->message = $res[1];
                    break;
                case 3:
                    $this->data    = $res[0];
                    $this->code    = $res[1];
                    $this->message = $res[2];
            }
        } catch (Exception $e) {
            // 回滚事务
            $this->trans && Db::rollback();

            $this->data    = null;
            $this->code    = $e->getCode() ?: 400;
            $this->message = $e->getMessage();
        }
        return $this;
    }

    /**
     * 开启事务
     */
    public function trans()
    {
        $this->trans = true;
        return $this;
    }

    /**
     * @return \think\response\Json
     */
    public function result()
    {
        return result($this->data, $this->code, $this->message);
    }
}
