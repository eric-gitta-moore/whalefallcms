<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\common\controller;

use app\service\UserService;
use think\Cache;
use think\Config;
use think\Cookie;
use think\View;

/**
 * 前台控制器基类
 * @package app\common\controller
 */
class BaseFrontend extends Frontend
{
//    /**
//     * 当前模板路径
//     * @var string
//     */
//    public $tpl;

//    /**
//     * 当前访问是否为手机
//     * @var bool
//     */
//    public $isMobile;
//
//    /**
//     * 是否为MIP访问
//     * @var bool
//     */
//    public $isMIP = false;
//
//    /**
//     * 系统设置
//     * @var array
//     */
//    public $config;


    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 当前模块
     * cartoon
     * novel
     * listen
     * @var string
     */
    public $module = 'cartoon';

    protected $cmod_array = [
        'index' => [
            '*' => 'portal',
        ],
        'user' => [
            '*' => 'center',
        ],
        'bookcase' => [
            'index' => 'bookcase',
            'history' => 'bookcase',

        ],

        'cartoon.book' => [
            '*' => 'detail'
        ],
        'novel.book' => [
            '*' => 'detail'
        ],
        'listen.book' => [
            '*' => 'detail'
        ],

        'cartoon.chapter' => [
            '*' => 'chapter'
        ],
        'novel.chapter' => [
            '*' => 'chapter'
        ],
        'listen.chapter' => [
            '*' => 'chapter'
        ],

        'cartoon.rank' => [
            '*' => 'rank'
        ],
        'novel.rank' => [
            '*' => 'rank'
        ],
        'listen.rank' => [
            '*' => 'rank'
        ],

        'cartoon.free' => [
            '*' => 'free'
        ],
        'novel.free' => [
            '*' => 'free'
        ],
        'listen.free' => [
            '*' => 'free'
        ]
    ];

    /**
     * 推广判断
     */
    private function invite()
    {
        $id = $this->request->param('pid/d');
        if ($id) {
            Cookie::set("inviter", $id,[
                'expire' => 0
            ]);
        }
    }

    protected function initSeoSetting()
    {
        $append = '';
        $module = $this->module;
        if (strtolower($this->request -> controller()) == 'index')
            $module = 'index';

        if (stripos($this->request -> controller(),'book') !== false)
            $append = '_book';
        elseif (stripos($this->request -> controller(),'chapter') !== false)
            $append = '_chapter';

        $base_key_name = 'site.' . $module . $append . '_seo_';
        $title_key_name = $base_key_name . 'title';
        $keywords_key_name = $base_key_name . 'keywords';
        $description_key_name = $base_key_name . 'description';

        //赋值全局
        $this->view -> seo_title = '';
        $this->view -> seo_keywords = '';
        $this->view -> seo_description = '';

        //解析变量
        if (\config('?' . $title_key_name))
        {
            $this->view -> title = str_replace('{site_name}',\config('site.name'),\config($title_key_name));
        }
        if (\config('?' . $keywords_key_name))
        {
            $this->view -> keywords = str_replace('{site_name}',\config('site.name'),\config($title_key_name));
        }
        if (\config('?' . $description_key_name))
        {
            $this->view -> description = str_replace('{site_name}',\config('site.name'),\config($title_key_name));
        }
    }

    public function _initialize()
    {
        parent::_initialize();

        $this->initSeoSetting();

//        $this->config['site'] = config('site');

//        $this->tpl = DS . 'template' . DS;
//        if ($this->request -> isMobile())
//            $this->isMobile = true;
//        else
//            $this->isMobile = false;

//        $this->isMobile = true;

//        if ($this->isMobile)
//        {
//            $this->tpl .= config('site.mobile_tpl') . DS;
//        }
//        elseif (!$this->isMIP)
//        {
//            $this->tpl .= config('site.pc_tpl') . DS;
//        }
//        else
//        {
//            $this->tpl .= config('site.mip_tpl') . DS;
//        }

        //计划任务
        $this->autoTask();

        //推广
        $this->invite();

        $preview = $this->request->get('preview');
        $template = $this->request->get('template');
        $cache_name = md5($this->request->ip() . "template_module");
        $cache_value = cache($cache_name);
        $module = $this->request->module();
        $domain = $this->gethostdomain($this->request->host());
        $self_template = config('self_template.modules');
//        trace($self_template);

        if (($preview && $template) || $cache_value) {
            $template = $template ? $template : $cache_value;
            $tpl = $template;
        } else {
            $module_template = "";
            if ($self_template && isset($self_template[$module . "_" . $domain]) && isset($self_template[$module . "_" . $domain]['view_base'])) {
                $module_template = $module . "_" . $domain;
            } elseif ($self_template && isset($self_template[$module]) && isset($self_template[$module]['view_base'])) {
                $module_template = $module;
            }
            $tpl = $self_template[$module_template]['name'];
        }


//        View::share('tpl_root',str_replace('\\','/',$this->tpl));
//        halt(rtrim(config('site.cdnurl'),'/'));
        $assets_path = rtrim(config('site.cdnurl'),'/') . '/templates/assets/modules/' . $tpl;
//        trace($assets_path);
        $this->view->replace([
//            '__CDN__' => rtrim(config('site.cdnurl'),'/'),
//            '__IMG_CDN__' => rtrim(config('site.img_cdn_url'),'/'),
            '__ASSETS_PATH__' => $assets_path,
            '__STATIC_PATH__' => $assets_path . '/' . 'static',
        ]);

        //当前页面类型
        $action = strtolower($this->request->action());
        $controller = strtolower($this->request->controller());
        if (array_key_exists($controller, $this->cmod_array)) {
            if (array_key_exists($action, $this->cmod_array[$controller])) {
                $cmod = $this->cmod_array[$controller][$action];
            } elseif (array_key_exists('*', $this->cmod_array[$controller])) {
                $cmod = $this->cmod_array[$controller]['*'];
            } else {
                $cmod = 'unkonw';
            }
        } else {
            if (array_key_exists('*', $this->cmod_array)) {
                $cmod = $this->cmod_array['*'];
            } else {
                $cmod = 'unkonw';
            }
        }


        //重载配置
        $config = $this->view->__get('config');
        $config['jsname'] = $assets_path . '/' . strtolower($controller) . '.js';
        $config['site']['score_name'] = \config('site.score_name');
        $this->view->__set('config', $config);

        //全局底部广告
        $Adszone = new \addons\adszone\library\Adszone();
        $ad_global_bottom = $Adszone->getAdsByMark('global_bottom'); //按照标记调用广告位

        //当前访问模块，cartoon，novel，listen
        define('MODULE', $this->module);
        View::share([
            'module' => MODULE,
            'is_login' => $this->auth->isLogin(),
            'cmod' => $cmod,
            'user_info' => $this->auth->getUser(),
            'actionname' => $this->request->action(),
            'controllername' => $this->request->controller(),
            'is_sign' => is_null($this->auth->getUser())?false:UserService::is_sign($this->auth->getUser()),
            'ad_global_bottom' => $ad_global_bottom,
        ]);

//        trace(config('template'));
//        halt(config('template'));
    }

    private function autoTask()
    {
        $urls = [];
        $urls[] = addon_url('qnbuygroup/Qnbuygroupautotask/index');
        if (cache('auto_task') != 1)
        {
            Cache::set('auto_task',1,\config('site.auto_task_period')?:60);
        }
        else
        {
            $urls = [];
        }
        View::share('auto_task_urls',$urls);
    }


    /**
     * 分解域名并获取
     * @param string $url
     * @param int $num 1是顶级2是二级，3是三级
     * @return mixed|string
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