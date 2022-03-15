<?php

namespace addons\recharge;

use app\common\library\Menu;
use think\Addons;
use think\Request;

/**
 * 余额充值插件
 */
class Recharge extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'user/moneylog',
                'title'   => '会员余额日志',
                'icon'    => 'fa fa-money',
                'sublist' => [
                    ['name' => 'user/moneylog/index', 'title' => '查看'],
                    ['name' => 'user/moneylog/add', 'title' => '添加'],
                    ['name' => 'user/moneylog/edit', 'title' => '修改'],
                    ['name' => 'user/moneylog/del', 'title' => '删除'],
                    ['name' => 'user/moneylog/multi', 'title' => '批量更新'],
                ]
            ],
            [
                'name'    => 'user/scorelog',
                'title'   => '会员积分日志',
                'icon'    => 'fa fa-circle-o',
                'sublist' => [
                    ['name' => 'user/scorelog/index', 'title' => '查看'],
                    ['name' => 'user/scorelog/add', 'title' => '添加'],
                    ['name' => 'user/scorelog/edit', 'title' => '修改'],
                    ['name' => 'user/scorelog/del', 'title' => '删除'],
                    ['name' => 'user/scorelog/multi', 'title' => '批量更新'],
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
        Menu::delete('user/moneylog');
        Menu::delete('user/scorelog');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('user/moneylog');
        Menu::enable('user/scorelog');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('user/moneylog');
        Menu::disable('user/scorelog');
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
