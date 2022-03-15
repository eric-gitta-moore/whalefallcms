<?php

namespace addons\development;


use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Development extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [];
        $config_file = ADDON_PATH . "development" . DS . 'config' . DS . "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if ($menu) {
            Menu::create($menu);
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $info = get_addon_info('development');
        Menu::delete(isset($info['first_menu']) ? $info['first_menu'] : 'development');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info = get_addon_info('development');
        Menu::enable(isset($info['first_menu']) ? $info['first_menu'] : 'development');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info = get_addon_info('development');
        Menu::disable(isset($info['first_menu']) ? $info['first_menu'] : 'development');
    }
}
