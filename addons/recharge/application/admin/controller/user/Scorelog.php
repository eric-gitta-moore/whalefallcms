<?php

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 积分变动管理
 *
 * @icon fa fa-circle-o
 */
class Scorelog extends Backend
{

    /**
     * Log模型对象
     * @var \app\admin\model\user\score\Log
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\user\score\Log;
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function add()
    {
        if ($this->request->isPost()) {
            $row = $this->request->post("row/a");
            $user_id = isset($row['user_id']) ? $row['user_id'] : 0;
            $score = isset($row['score']) ? $row['score'] : 0;
            $memo = isset($row['memo']) ? $row['memo'] : '';
            if (!$user_id || !$score) {
                $this->error("积分和会员ID不能为空");
            }
            \app\common\model\User::score($score, $user_id, $memo);
            $this->success("添加成功");
        }
        return parent::add();
    }

}
