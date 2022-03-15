<?php
namespace addons\customizerequirejsconfig;

use app\common\library\Auth;
use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Customizerequirejsconfig extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu=[];
        $config_file= ADDON_PATH ."customizerequirejsconfig" . DS.'config'.DS. "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if($menu){
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
        $info=get_addon_info('customizerequirejsconfig');
        Menu::delete(isset($info['first_menu'])?$info['first_menu']:'customizerequirejsconfig');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info=get_addon_info('customizerequirejsconfig');
        Menu::enable(isset($info['first_menu'])?$info['first_menu']:'customizerequirejsconfig');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info=get_addon_info('customizerequirejsconfig');
        Menu::disable(isset($info['first_menu'])?$info['first_menu']:'customizerequirejsconfig');
    }

    public function configInit(&$config)
    {
        $addon_config = get_addon_config('customizerequirejsconfig');
//        halt($addon_config);
        foreach ($addon_config['extra_confg'] as $item) {
//            halt($item);
            $config[str_replace('.','_',$item)] = config($item);
        }

        $config['user'] = [];

        $auth = Auth::instance();

        // token
        $token = request()->server('HTTP_TOKEN', request()->request('token', \think\Cookie::get('token')));
        //初始化
        if ($auth->init($token))
        {
//            $user = $auth -> getUser();
//            halt();
            $config['user'] = $auth -> getUserinfo();
        }
//        $config['user']['id']
    }
}
