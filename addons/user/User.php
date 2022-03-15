<?php

namespace addons\user;

use app\common\library\Menu;
use think\Addons;

/**
 * 会员扩展插件
 */
class User extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name' => 'user/extra',
                'title' => '用户扩展',
                'icon' => 'fa fa-file-text-o',
                'sublist' => [
                    [
                        'name' => 'user/level',
                        'title' => '积分等级',
                        'icon' => 'fa fa-file-text-o',
                        'sublist' => [
                            ['name' => 'user/level/index', 'title' => 'View'],
                        ]
                    ],
                    [
                        'name' => 'user/log',
                        'title' => '用户日志',
                        'icon' => 'fa fa-file-text-o',
                        'sublist' => [
                            ['name' => 'user/log/index', 'title' => 'View'],
                            ['name' => 'user/log/del', 'title' => 'Del'],
                        ]
                    ],

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
        return Menu::delete('user/extra')
            && Menu::delete('user/log')
            && Menu::delete('user/level');

    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        return Menu::enable('user/extra')
            && Menu::enable('user/level')
            && Menu::enable('user/log');
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        return Menu::disable('user/extra')
            && Menu::disable('user/level')
            && Menu::disable('user/log');

    }

    /**
     * 原生会员中心边栏后
     * @return mixed
     * @throws \Exception
     */
    public function userSidenavAfter()
    {
        //从原生会员中心跳到扩展会员中心
        return $this->fetch('view/hook/user_sidenav_after');
    }

}
