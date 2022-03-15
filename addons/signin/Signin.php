<?php

namespace addons\signin;

use app\common\library\Menu;
use app\common\model\User;
use fast\Date;
use think\Addons;
use think\Config;
use think\Request;
use think\Route;

/**
 * 签到插件
 */
class Signin extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'signin',
                'title'   => '签到管理',
                'icon'    => 'fa fa-map-marker',
                'sublist' => [
                    [
                        "name"  => "signin/index",
                        "title" => "查看"
                    ],
                    [
                        "name"  => "signin/add",
                        "title" => "添加"
                    ],
                    [
                        "name"  => "signin/edit",
                        "title" => "编辑"
                    ],
                    [
                        "name"  => "signin/del",
                        "title" => "删除"
                    ],
                    [
                        "name"  => "signin/multi",
                        "title" => "批量更新"
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
        Menu::delete("signin");
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("signin");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable("signin");
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
        $controllername = strtolower($request->controller());
        $actionname = strtolower($request->action());
        $data = [
            'actionname'     => $actionname,
            'controllername' => $controllername
        ];
        return $this->fetch('view/hook/user_sidenav_after', $data);
    }

}
