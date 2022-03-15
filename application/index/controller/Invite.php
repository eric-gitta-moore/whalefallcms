<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\index\controller;

use app\common\controller\BaseFrontend;
use app\common\controller\Frontend;

class Invite extends BaseFrontend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = '*';
    protected $layout = 'user';


    public function index()
    {
        $inviteList = \addons\invite\model\Invite::
        where(['user_id' => $this->auth->id])
            ->with('invited')
            ->order('id desc')
            ->paginate(10);

        $inviteConfig = get_addon_config('invite');
        $this->view->assign('title', "邀请好友");
        $this->view->assign('sub_title', "邀请好友");
        $this->view->assign('inviteList', $inviteList);
        $this->view->assign('inviteConfig', $inviteConfig);
        return $this->view->fetch();
    }

}
