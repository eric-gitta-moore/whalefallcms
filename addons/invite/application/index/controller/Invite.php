<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Invite extends Frontend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = 'default';


    public function index()
    {
        $inviteList = \addons\invite\model\Invite::
        where(['user_id' => $this->auth->id])
            ->with('invited')
            ->order('id desc')
            ->paginate(10);

        $inviteConfig = get_addon_config('invite');
        $this->view->assign('title', "邀请好友");
        $this->view->assign('inviteList', $inviteList);
        $this->view->assign('inviteConfig', $inviteConfig);
        return $this->view->fetch();
    }

}
