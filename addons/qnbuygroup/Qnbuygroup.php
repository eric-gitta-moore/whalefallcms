<?php

namespace addons\qnbuygroup;

use app\common\library\Menu;
use think\Addons;
use think\Exception;
use think\Log;
use think\Request;

/**
 * 插件
 */
class Qnbuygroup extends Addons
{
    public function install()
    {
        $menu = [
            [
                'name' => 'qnbuygroup',
                'title' => 'VIP用户组',
                'sublist' => [
                    [
                        'name' => 'qnbuygroup/qngroupset',
                        'title' => '用户组设置',
                        'icon' => 'fa fa-users',
                        'sublist' => [
                            ['name' => 'qnbuygroup/qngroupset/index', 'title' => '查看'],
                            ['name' => 'qnbuygroup/qngroupset/add', 'title' => '添加'],
                            ['name' => 'qnbuygroup/qngroupset/edit', 'title' => '修改'],
                            ['name' => 'qnbuygroup/qngroupset/del', 'title' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'qnbuygroup/qngrouporder',
                        'title' => '用户组购买订单',
                        'icon' => 'fa fa-circle-o',
                        'sublist' => [
                            ['name' => 'qnbuygroup/qngrouporder/index', 'title' => '查看'],
                            ['name' => 'qnbuygroup/qngrouporder/del', 'title' => '删除'],
                        ]
                    ]
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('qnbuygroup');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('qnbuygroup');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('qnbuygroup');
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
