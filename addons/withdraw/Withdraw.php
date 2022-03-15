<?php

namespace addons\withdraw;

use app\common\library\Menu;
use think\Addons;
use think\Request;

/**
 * 余额提现插件
 */
class Withdraw extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'user/withdraw',
                'title'   => '会员提现管理',
                'icon'    => 'fa fa-money',
                'sublist' => [
                    ['name' => 'user/withdraw/index', 'title' => '查看'],
                    ['name' => 'user/withdraw/add', 'title' => '添加'],
                    ['name' => 'user/withdraw/edit', 'title' => '修改'],
                    ['name' => 'user/withdraw/del', 'title' => '删除'],
                    ['name' => 'user/withdraw/multi', 'title' => '批量更新'],
                ]
            ]
        ];
        Menu::create($menu, 'user');
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('user/withdraw');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('user/withdraw');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('user/withdraw');
        return true;
    }

    /**
     * 会员中心边栏后
     * @return mixed
     * @throws \Exception
     */
    public function userSidenavAfter()
    {
        $request = Request::instance();
        $actionname = strtolower($request->action());
        $data = [
            'actionname' => $actionname
        ];
        return $this->fetch('view/hook/user_sidenav_after', $data);
    }

}
