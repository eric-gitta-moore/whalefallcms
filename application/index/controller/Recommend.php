<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\index\controller;


use app\common\controller\BaseFrontend;
use app\service\BookService;

abstract class Recommend extends BaseFrontend
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'index'
    ];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [
        '*'
    ];

    /**
     * 当前模块
     * cartoon
     * novel
     * listen
     * @var string
     */
    public $module = 'cartoon';

    public function index($page=1)
    {
        $data = BookService::getBooks($this->module,
            ['recommend_switch' => 1],
            '1=1',
            'readnum desc',
            10,
            true,
            true,
            true,
            10,
            [
                'page' => $page,
            ]);

        trace($data -> toArray());
//        halt(1);


        return view('',[
            'paginate' => $data
        ]);
    }
}