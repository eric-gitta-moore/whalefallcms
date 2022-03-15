<?php

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 会员积分变动管理
 *
 * @icon fa fa-circle-o
 */
class ScoreLog extends Userend
{

    /**
     * Log模型对象
     * @var \app\common\model\user\Scorelog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\user\Scorelog;

    }

}
