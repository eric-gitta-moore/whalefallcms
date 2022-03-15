<?php

namespace addons\templates;

use addons\templates\library\Service;
use app\common\library\Menu;
use think\Addons;
use think\Request;

/**
 * 插件
 */
class Templates extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [];
        $config_file = ADDON_PATH . "templates" . '/' . 'config' . '/' . "menu.php";
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
        $info = get_addon_info('templates');
        Menu::delete(isset($info['first_menu']) ? $info['first_menu'] : 'templates');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info = get_addon_info('templates');
        Menu::enable(isset($info['first_menu']) ? $info['first_menu'] : 'templates');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info = get_addon_info('templates');
        Menu::disable(isset($info['first_menu']) ? $info['first_menu'] : 'templates');
    }


    /**
     * 应用初始化标签位    系统
     */
    public function appInit()
    {

    }


    /**
     * 模块初始化
     */
    public function moduleInit($request)
    {
        $ignore_dir = ['api', 'common', 'extra', 'admin'];//需要忽略的文件夹
        $module = $request->module();
        if ($module && !in_array($module, $ignore_dir)) {
            //获取二级域名
            $domian = $this->gethostdomain($request->host());
            $preview = $request->get('preview');
            $template = $request->get('template');
            $cache_name = md5($request->ip() . "template_module");
            $cache_value = cache($cache_name);

            //设置模板

            //是否是预览模式
            if (($preview && $template) || $cache_value) {
                $config = $this->getConfig();
                if ($config['preview'] == 0) {
                    echo "如果需要预览模板请先开启";
                    exit();
                }
                $template = $template ? $template : $cache_value;
                \think\Config::set('template.view_base', ".." . '/' . "public" . '/' . "templates" . '/' . "modules" . '/' . $template . '/');
                cache($cache_name, $template, 600);//缓存模板 预览10分钟

                $service = new Service();
                $templates_config = $service->getConfigKeyVal($template, 'modules');
                \think\Config::set('templates', $templates_config);
            } else {
                $self_template = config('self_template.modules');
                $module_template = "";
                if ($self_template && isset($self_template[$module . "_" . $domian]) && isset($self_template[$module . "_" . $domian]['view_base'])) {
                    $module_template = $module . "_" . $domian;
                } else if ($self_template && isset($self_template[$module]) && isset($self_template[$module]['view_base'])) {
                    $module_template = $module;
                }
                if ($module_template) {
                    \think\Config::set('template.view_base', $self_template[$module_template]['view_base']);
                    $service = new Service();
                    $templates_config = $service->getConfigKeyVal($self_template[$module_template]['name'], 'modules');
                    \think\Config::set('templates', $templates_config);
                }
            }

        }

    }

    /**
     * 插件模块初始化
     */
    public function addonModuleInit($request)
    {
        //获取二级域名
        $domian = $this->gethostdomain($request->host());

        $dispatch = $request->dispatch();
        $route = $request->route();

        if (isset($dispatch['var']) && $dispatch['var'] && isset($dispatch['var']['addon'])) {
            $addon = $dispatch['var']['addon'];
        } else if ($route && isset($route['addon'])) {
            $addon = $route['addon'];
        } else {
            return true;
        }
        $preview = $request->get('preview');
        $template = $request->get('template');
        $cache_name = md5($request->ip() . "template_addon");
        $cache_value = cache($cache_name);
        //是否是预览模式
        if (($preview && $template) || $cache_value) {
            $config = $this->getConfig();
            if ($config['preview'] == 0) {
                echo "如果需要预览模板请先开启";
                exit();
            }
            $template = $template ? $template : $cache_value;
            \think\Config::set('template.view_base', ".." . '/' . "public" . '/' . "templates" . '/' . "addons" . '/' . $template . '/' . $addon . '/');
            cache($cache_name, $template, 600);//缓存模板 预览10分钟

            $service = new Service();
            $templates_config = $service->getConfigKeyVal($template, 'addons');
            \think\Config::set('templates', $templates_config);
        } else {
            $self_template = config('self_template.addons');
            $addon_template = "";
            if ($self_template && isset($self_template[$addon . "_" . $domian])) {
                $addon_template = $addon . "_" . $domian;
            } elseif ($self_template && isset($self_template[$addon])) {
                $addon_template = $addon;
            }
            if ($addon_template) {
                \think\Config::set('template.view_base', ($self_template[$addon_template]['view_base'] . '/' . $addon . '/'));
                $service = new Service();
                $templates_config = $service->getConfigKeyVal($self_template[$addon_template]['name'], 'addons');
                \think\Config::set('templates', $templates_config);
            }
        }
    }

    /**
     * 分解域名并获取
     * @param unknown $url
     * @param number $num 1是顶级2是二级，3是三级
     * @return Ambigous <number, multitype:>
     */
    private function gethostdomain($url, $num = 2)
    {
        $num++;
        $host_arr = explode('.', $url);
        //判断是否是双后缀
        $zi_tow = false;
        $host_cn = 'com.cn,net.cn,org.cn,gov.cn';
        $host_cn = explode(',', $host_cn);
        foreach ($host_cn as $host) {
            if (strstr($url, $host)) {
                $zi_tow = true;
            }
        }
        if ($zi_tow) {
            $num++;
        }
        $host_count = count($host_arr);
        if ($host_count < $num) return '';

        return $host_arr[$host_count - $num];
    }
}
