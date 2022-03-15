<?php

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 用户日志
 *
 * @icon fa fa-circle-o
 */
class Log extends Userend
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

    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }


}
