<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\index\controller;


use app\admin\model\qnbuygroup\Order;
use app\common\controller\BaseFrontend;
use app\service\BookService;
use app\service\CategoryService;
use app\service\CateService;
use think\View;

abstract class Cate extends BaseFrontend
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

    public function _initialize()
    {
        parent::_initialize();
        $raw_path = $this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action();
        $baseurl = url($raw_path);
        View::share('baseurl', $baseurl);
        View::share('raw_path', $raw_path);
    }

    /**
     * @param int $cate 分类
     * @param int $end status 漫画完结状态:0=连载,1=完结
     * @param int $attribute free_type 收费类型:0=免费,1=收费
     * @param int $page
     * @return \think\response\View
     */
    public function index($cate = -1,$end=-1,$attribute=-1, $page = 1)
    {
        $data = CateService::getCate($this->module);

        $where = [];
        $cate_where = [];
        if ($cate != -1)
        {
            $cate_where['id'] = $cate;
        }
        if ($end != -1)
        {
            $where['status'] = $end;
        }
        if ($attribute != -1)
        {
            $where['free_type'] = $attribute;
        }

        $query_data = [];
        $query_data = BookService::getBooks($this->module,
            $where,
            $cate_where,
            'log_time desc',
            15,
            true,
            true,
            true,
            15,
            [
                'page' => $page
            ]);

        //今日更新
        $today_new_books_count = BookService::getBookNamespace($this->module)::whereTime('log_time','today') -> count();

        //本周更新
        $week_new_books_count = BookService::getBookNamespace($this->module)::whereTime('log_time','week') -> count();

//        trace(collection($data)->toArray());
//        trace(collection($query_data)->toArray());
        trace([
            'cate_data' => $data,
            'cate' => $cate,
            'end' => $end,
            'attribute' => $attribute,
            'page' => $page,
            'query_data' => $query_data,
            'today_new_books_count' => $today_new_books_count,
            'week_new_books_count' => $week_new_books_count,
        ]);

        return view('', [
            'cate_data' => $data,
            'cate' => $cate,
            'end' => $end,
            'attribute' => $attribute,
            'page' => $page,
            'query_data' => $query_data,
            'today_new_books_count' => $today_new_books_count,
            'week_new_books_count' => $week_new_books_count,
        ]);
    }
}