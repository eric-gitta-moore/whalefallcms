<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\api\controller;


use app\common\controller\Api;
use app\service\BookService;
use think\Paginator;

abstract class Book extends Api
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'get',
        'getBooks',
        'getChapterList',
        'getBookChapter',
        'getBookDetail',
    ];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [
        '*'
    ];

    /**
     * 可以为cartoon，novel，listen
     * @var string 类型，小写
     */
    protected $module = 'cartoon';

    public function get($id)
    {
        $r = BookService::getBookDetail($id,$this->module);
        if (is_null($r))
            $this->error('没有这本书',$r);
        $this->success('获取成功',BookService::getBookDetail($id,$this->module)['chapter']);
    }

    public function getBookDetail($id)
    {
        $r = BookService::getBookDetail($id,$this->module);
        if (is_null($r))
            $this->error('没有这本书',$r);
        $this->success('获取成功',BookService::getBookDetail($id,$this->module));
    }

    public function getBookChapter($id)
    {
        $r = BookService::getBookDetail($id,$this->module);
        if (is_null($r))
            $this->error('没有这本书',$r);
        $this->success('获取成功',BookService::getBookDetail($id,$this->module)['chapter']);
    }

    /**
     * @param int $page
     * @param int $size
     * @param string $order
     * @param bool $cate
     * @param bool $reader
     * @param bool|int $status 漫画完结状态:0=连载,1=完结
     */
    public function getBooks($page=1,$size=6,$order='id desc',$cate=false,$reader=false,$status=false)
    {
        $where = [];
        $cate_where = [];
        if (!empty($cate) && $cate != 'all')
        {
            $cate_where['id'] = $cate;
            $cate_where['status'] = $this->module;
        }
        if ($reader == 'adult')
            $where['18plus'] = '1';
        if (!empty($status) && $status != 'all')
            $where['status'] = $status;

        $order_text = '';
        switch ($order)
        {
            case 'log_time'://刚刚更新
                $order_text = 'log_time desc';
                break;

            case 'recommend'://推荐
                $order_text = 'recommend_switch desc,readnum desc';
                break;

            case 'collectnum'://追更
                $order_text = 'collectnum desc';
                break;

            case 'readnum'://热门
                $order_text = 'readnum desc';
                break;


            case 'id'://新作
            case 'createtime':
            default:
                $order_text = 'createtime desc';


        }

        $r = BookService::getBooks($this->module,
            $where,$cate_where,$order_text,$size,true,false,
            true,$size,[
                'page' => $page
            ]);

//        $r = BookService::getBooks($this->module,'1=1','1=1',$order,$size,true,false,true,$size,['page' => $page]);
        $this->success('获取成功',$r);
    }

    public function getChapterList($id=1,$page=1,$size=10,$orderby='asc')
    {
        if ($orderby != 'asc' && $orderby != 'desc')
        {
            $this->error('排序字段有误');
        }

        $book_foreign_key = $this->module . '_' . $this->module . '_id';
        $chapter_model = 'app\common\model\\' . $this->module . '\Chapter';
        $book_model = 'app\common\model\\' . $this->module . '\\' . ucfirst($this->module);

        //用户购买记录
        if (!is_null($this->auth))
        {
            $user = $this->auth -> getUser();

        }
        
        $book = $book_model::get($id);
        if (is_null($book))
            $this->error('本书不存在');

        $result = $chapter_model::where([
            $book_foreign_key => $id
        ]) -> order('weigh ' . $orderby) -> paginate($size,false,[
            'page' => $page
        ]) -> toArray();

        if ($book['start_pay'] - 1 > 0)
        {
            $result2 = $chapter_model::where([
                $book_foreign_key => $id
            ]) -> order('weigh') -> limit($book['start_pay'] - 1) -> column('id');
        }

        if (isset($result2))
        {
            foreach ($result['data'] as &$item) {
                if (in_array($item['id'],$result2))
                    $item['money'] = 0;
            }
        }

        $this->success('获取成功',$result);
    }
}