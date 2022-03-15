<?php

namespace addons\adszone;

use app\common\library\Menu;
use think\Addons;

/**
 * 在线命令插件
 */
class Adszone extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'adszone',
                'title'   => '海报&广告管理',
                'icon'    => 'fa fa-pie-chart',
                'sublist' => [
                    ['name' => 'adszone/index', 'title' => '广告位列表'],
                    ['name' => 'adszone/add', 'title' => '添加广告位'],
                    ['name' => 'adszone/edit', 'title' => '编辑广告位'],
                    ['name' => 'adszone/del', 'title' => '删除广告位'],
                    ['name' => 'adszone/ads', 'title' => '广告内容管理'],
                    ['name' => 'adszone/ads_add', 'title' => '添加广告内容'],
                    ['name' => 'adszone/ads_edit', 'title' => '编辑广告内容'],
                    ['name' => 'adszone/ads_del', 'title' => '删除广告内容'],
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
        Menu::delete('adszone');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('adszone');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('adszone');
        return true;
    }

}