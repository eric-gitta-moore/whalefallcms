<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\index\controller;


use app\common\controller\BaseFrontend;
use think\View;

class Exchange extends BaseFrontend
{
    /**
     * 布局模板
     * @var string
     */
    protected $layout = 'user';

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $config = get_addon_config('moneytoscore');
        foreach ($config['tips_list'] as &$item) {
            $item = str_replace('{$cny_to_score}',$config['cny_to_score'],$item);
            $item = str_replace('{$score_name}',$config['score_name'],$item);
        }
        $this->view->assign([
            'addonConfig' => $config,
            'title' => $config['score_name'] .  __('兑换'),
        ]);
    }

    public function exchange()
    {

        return view();
    }
}