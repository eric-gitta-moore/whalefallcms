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

abstract class Rank extends BaseFrontend
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'index',
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

    public function index($order = 'readnum',$page=1)
    {
        //合法的排序列表，键名为表单属性，键值为数据库对应字段
        $order_list = [
            'readnum' => 'readnum desc',
            'createtime' => 'createtime desc',
            'end' => ['status' => 1],//漫画完结状态:0=连载,1=完结
            'free' => ['free_type' => 0],
            'boy' => config('site.rank_boy_cate')?:'readnum desc',
            'girl' => config('site.rank_girl_cate')?:'readnum desc',
        ];

//        $data = [];
        $field = $order_list[$order];
        if (empty($field))
            $this->error('非法参数');


        if (is_array($field))
        {
            //where条件
//            halt($field);
            $data = BookService::getBooks($this->module,
                $field,
                '1=1',
                'readnum desc',
                15,
                true,
                true,
                true,
                15,
                [
                    'page' => $page,
                ]);
        }
        elseif (intval($field) !== 0)
        {
            //cate条件
            $data = BookService::getBooks($this->module,
                '1=1',
                ['id' => $field],
                'readnum desc',
                15,
                true,
                true,
                true,
                15,
                [
                    'page' => $page,
                ]);
        }
        elseif (is_string($field))
        {
            //order条件
            $data = BookService::getBooks($this->module,
                '1=1',
                '1=1',
                $field,
                15,
                true,
                true,
                true,
                15,
                [
                    'page' => $page,
                ]);
        }
        else
        {
            $data = null;
        }


        trace($data -> toArray());
        trace($data -> render());

        return view('',[
            'order' => $order,
//            'data' => $data,
            'paginate' => $data
        ]);
    }

}