<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 用户日志
 *
 * @icon fa fa-circle-o
 */
class Log extends Backend
{

    /**
     * Log模型对象
     * @var \app\common\model\user\Log
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\user\Log;

    }

}
