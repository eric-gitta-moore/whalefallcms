<?php
namespace addons\defaultusergroup;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Defaultusergroup extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu=[];
        $config_file= ADDON_PATH ."defaultusergroup" . DS.'config'.DS. "menu.php";
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
        $info=get_addon_info('defaultusergroup');
        Menu::delete(isset($info['first_menu'])?$info['first_menu']:'defaultusergroup');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info=get_addon_info('defaultusergroup');
        Menu::enable(isset($info['first_menu'])?$info['first_menu']:'defaultusergroup');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info=get_addon_info('defaultusergroup');
        Menu::disable(isset($info['first_menu'])?$info['first_menu']:'defaultusergroup');
    }

    public function userRegisterSuccessed(&$user_model)
    {
        if ($user_model -> group_id == 0)
        {
            $default = get_addon_config('defaultusergroup');
            if (isset($default['default_user_group_id']))
            {
                $user_model -> group_id = $default['default_user_group_id'];
                $user_model -> save();
            }
//            var_dump($user_model -> group_id);
//                $user_model -> setField('group_id',$default['default_user_group_id']);
        }
    }
}
