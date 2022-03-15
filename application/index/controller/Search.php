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
use think\Paginator;
use think\Request;

abstract class Search extends BaseFrontend
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

    public function index()
    {
        $search_word = input('search_word', '');
        $page = input('page', 1);
        $list = [];
        $recommend = '';

        if (!empty($search_word)) {
            $list = BookService::search($search_word, $this->module, 10, false, true, 10, [
                'page' => $page
            ]);
        }

        //推荐
        $cnt = is_object($list)?$list -> total():(is_array($list)?count($list):0);
        if ($cnt && !empty($search_word))
        {
            $recommend = BookService::getBooks($this->module,'1=1','1=1','rand()',10,true,true);
        }

        return view('',[
            'page' => $page,
            'list' => $list,
            'search_word' => $search_word,
            'recommend' => $recommend,
        ]);
    }
}