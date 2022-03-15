<?php
/**
 * 云凌鲸落小说漫画聚合分销CMS系统
 * @Author Curtis - 云凌工作室
 * @Website http://www.whalefallcms.com
 * @Datetime 2020/4/8 下午 05:07
 */


namespace app\index\controller;


use app\common\controller\BaseFrontend;
use app\common\model\cartoon\Cartoon;
use app\common\model\novel\Novel;
use app\common\model\user\Buylog;
use app\service\CollectionService;

class Bookcase extends BaseFrontend
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
//        'index'
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
    public $module = 'bookcase';

    public function index()
    {
        $user = $this->auth->getUser();
        $collection = CollectionService::getUserCollection($user->id, $this->module, true);

        trace($collection);
//        halt($collection);

        return view('', [
            'collection' => $collection,

        ]);
    }

    public function history()
    {
        return view();
    }

    //消费记录
    public function consumption()
    {

        return view('',[

        ]);
    }


}