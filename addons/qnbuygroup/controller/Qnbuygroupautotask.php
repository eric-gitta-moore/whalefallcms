<?php

namespace addons\qnbuygroup\controller;

use app\common\model\User;
use think\addons\Controller;
use addons\qnbuygroup\model\Buygroupuser;
use think\Db;
use Exception;

class Qnbuygroupautotask extends Controller
{
    protected $model = null;

    /**
     * 初始化方法,最前且始终执行
     */
    public function _initialize()
    {

        // 只可以以cli方式执行
//        if (!$this->request->isCli()) {
//            $this->error('Autotask script only work at client!');
//        }

        parent::_initialize();

        $this->model = new Buygroupuser;

        // 清除错误
        error_reporting(0);

        // 设置永不超时
        set_time_limit(0);
    }

    public function index()
    {
        $usergroupList = $this->model->with('groupset')
            ->where("expiredtime", '<=', time())
            ->where('status', 'normal')
            ->select();

        foreach ($usergroupList as $usergroup) {
            $user = User::get($usergroup['user_id']);
            if ($user) {
                Db::startTrans();
                try {
                    $user->save(['group_id' => $usergroup['groupset']['expgroup_id']]);
                    $usergroup->save(['status' => 'expired']);
                    Db::commit();
                } catch (Exception $e) {
                    Db::rollback();
                }
            }
        }
    }
}
