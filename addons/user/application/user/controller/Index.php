<?php

namespace app\user\controller;

use app\common\controller\Userend;
use app\common\model\user\Log;
use think\Config;
use think\Hook;
use think\Validate;

/**
 * 会员中心入口文件
 */
class Index extends Userend
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }
    }


    /**
     * 会员中心
     */
    public function index()
    {
        //左侧菜单
        list($menulist, $navlist, $fixedmenu, $referermenu) = $this->auth->getSidebar([
            'dashboard' => 'hot',
        ], $this->view->site['fixedpage']);
        $action = $this->request->request('action');
        if ($this->request->isPost()) {
            if ($action == 'refreshmenu') {
                $this->success('', null, ['menulist' => $menulist, 'navlist' => $navlist]);
            }
        }
        $this->view->assign('menulist', $menulist);
        $this->view->assign('navlist', $navlist);
        $this->view->assign('fixedmenu', $fixedmenu);
        $this->view->assign('referermenu', $referermenu);
        $this->view->assign('title', __('User center'));
        return $this->view->fetch();
    }

    /**
     * 登录
     */
    public function login()
    {
        $this->redirect("index/user/login");
    }

    /**
     * 注销
     */
    public function logout()
    {
        //注销本站
        Log::setTitle(__('Logout'));
        $this->auth->logout();
        $synchtml = '';
        ////////////////同步到Ucenter////////////////
        if (defined('UC_STATUS') && UC_STATUS) {
            $uc = new \addons\ucenter\library\client\Client();
            $synchtml = $uc->uc_user_synlogout();
        }
        $this->success(__('Logout successful') . $synchtml, url('user/index/index'));
    }
}
