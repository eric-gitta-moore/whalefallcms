<?php

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 个人资料
 */
class Profile extends Userend
{

    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->auth->level) $this->assign('user', $this->auth->getLevel());
        return $this->view->fetch('user/profile');
    }

}
