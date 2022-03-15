<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


use app\common\model\cartoon\Cartoon;
use app\common\model\cartoon\Chapter;
use app\common\model\cartoon\Relation;
use app\common\model\config\Cate;
use app\common\model\config\Pageset;
use app\common\model\user\Collection;
use PDOStatement;
use think\Db;
use think\Model;

class BookService
{
    /**
     * 获取书籍及其分类
     * @param string $type 类型:小说,漫画,听书
     * @param array|string $where 书籍限制条件
     * @param array|string $cate_where 分类限制条件
     * @param string $order 排序
     * @param int $limit 限制数量
     * @param bool $cache 是否缓存
     * @param bool $with_author
     * @param bool $paginate
     * @param int $page_size
     * @param array $page_config
     * @return bool|array
     */
    public static function getBooks($type = 'cartoon',
                                    $where = '1=1',
                                    $cate_where = '1=1',
                                    $order = 'readnum desc',
                                    $limit = 6,
                                    $cache = true,
                                    $with_author = false,
                                    $paginate = false,
                                    $page_size = 15,
                                    $page_config = [])
    {
        $namespace = 'app\common\model\\' . $type . '\\' . ucfirst($type);

        $with = [
            'Cate' => function ($query) use ($cate_where) {
                $query->where($cate_where);
            },
        ];
        if ($with_author) {
            $with['Author'] = function ($query) {
            };
        }
//        halt($where);
        $books = $namespace::with($with)
            ->limit($limit);

        if (!empty($where))
            $books->where($where);

        if (!empty($cate_where))
        {
            $relation_model = 'app\common\model\\' . $type . '\Relation';
            if (isset($cate_where['id']))
            {
                $cate_where = $cate_where['id'];
            }
            $sub_sql = $relation_model::where(['config_cate_id' => ['in',$cate_where]]) -> field($type . '_' . $type . '_id') -> buildSql();
//            halt($sub_sql);
            $books -> where('id in ' . $sub_sql);
//            $books -> where('id','in',$sub_sql);
        }

        if (strstr($order,'()') !== false)
        {
            $books -> orderRaw($order);
        }
        else
        {
            $books -> order($order);
        }

        if ($paginate !== false) {
            $data = $books->paginate($page_size, false, $page_config);
        } else {
            $data = $books->select();
        }
//halt($data);
//        halt((new $namespace) -> getLastSql());
        return $data;
    }

    public static function getBookNamespace($type)
    {
        $namespace = 'app\common\model\\' . $type . '\\' . ucfirst($type);
        return $namespace;
    }

    /**
     * 获取最新书籍
     * @param string $type 类型:小说,漫画,听书
     * @param bool $cate 书籍分类
     * @param string $order 排序
     * @param int $limit 限制数量
     * @param bool $cache 是否缓存
     * @return array|bool
     */
    public static function getNews($type = 'cartoon', $cate = false, $order = 'log_time desc', $limit = 6, $cache = true)
    {
        $namespace = 'app\common\model\\' . $type . '\\' . ucfirst($type);

        if ($cate !== false && $cate !== true) {
            if (is_array($cate)) {
                $cate = implode(',', $cate);
            } elseif (is_string($cate)) {
                $cate = trim(trim($cate, ','));
            } else {
                $cate = false;
            }
        }

        if ($cate === true) {
            $books = $namespace::with('Cate')->order($order)->limit($limit)->select();
        }
        elseif ($cate)
        {
            $books = $namespace::with('Cate', function ($query) use ($cate) {
                $query->where('id', 'in', $cate);
            })->order($order)->limit($limit)->select();
        }
        else
        {
            $books = $namespace::order($order)->limit($limit)->cache($cache, config('site.query_cache'))->select();
        }

//        if (!$books)
//            $books = null;

        return $books;
    }

    /**
     * 获取推荐漫画并以分类作为索引数组下标
     * @param string $page 所属页面
     * @param string $type 类型:小说,漫画,听书
     * @param string $order 排序
     * @param int $limit 限制数量
     * @param bool $cache 是否缓存
     * @return bool|false|PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getGoodsGroupByCate($page = 'portal', $type = 'cartoon', $order = 'readnum desc', $limit = 6, $cache = true)
    {
        //获取后台数据
        $key = $page . '_' . $type . '_goods';
        $page_set = Pageset::where('keyword', $key)->cache($cache, config('site.query_cache'))->value('config_cate_ids');//1,2

        if (!$page_set)
            return null;


        trace($limit);
        if ($page_set == 'all')
        {
            $books = Cate::with([
                ucfirst($type) => function ($query) use ($order, $limit) {
                    $query->order($order);//->limit($limit)
                }
            ])->select();//->limit($limit)
        }
        else
        {
            $books = Cate::with([
                ucfirst($type) => function ($query) use ($order, $limit) {
                    $query->order($order);//->limit($limit)
                }
            ])->where('id', 'in', $page_set)->select();//->limit($limit)
        }

        if (!$books)
            $books = false;

        return $books;
    }

    /**
     * 获取页面设置的键值
     * @param $key
     * @param string $type
     * @param string $value
     * @param bool $cache
     * @return mixed
     */
    public static function getPageConfig($key, $type = 'cartoon', $value = 'config_cate_ids', $cache = true)
    {
        $where = ['keyword' => $key, 'status' => $type];
        if ($cache)
            $page_set = Pageset::where($where)->cache($cache, config('site.query_cache'))->value($value);
        else
            $page_set = Pageset::where($where)->value($value);
        return $page_set;
    }

    /**
     * 获取书本详细
     * @param $id
     * @param string $type
     * @return array|bool|false|PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBookDetail($id, $type = 'cartoon')
    {
        $model = ucfirst(strtolower($type));

        if (!in_array($model, ['Cartoon', 'Novel', 'Listen']))
            return null;

        $model = 'app\common\model\\' . strtolower($model) . '\\' . $model;
        $detail = $model::with(['author', 'chapter', 'cate', 'reward'])->where('id', $id)->find();
//        if (is_null($detail['cate']))
//            $detail['cate'] = [];
        if (is_null($detail))
            return null;

        $detail['reward_count'] = RewardService::getBookRewards($id, $type);
        return $detail;

    }

    /**
     * 获取指定分类的随机书本
     * 可指定排序，默认随机
     * @param string $type
     * @param array $cate_id
     * @param int $limit
     * @param string $order
     * @param bool $raw
     * @return false|PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRandomBooks($type = 'cartoon', $cate_id = [], $limit = 6, $order = 'rand()', $raw = false)
    {
        $where = [];
        if (!empty($cate_id))
            $where['config_cate_id'] = ['in', implode(',', $cate_id)];

//        if ($type !== false) {
//            $type = strtolower($type);
//            $where['status'] = $type;
//        }

//        $limit = 1;
//        $order = 'rand()';

        $relation_model = 'app\common\model\\' . strtolower($type) . '\Relation';
        $categories = $relation_model::where($where) -> column($type . '_' . $type . '_id');
//        var_dump($categories_sql);
        $book_model = 'app\common\model\\' . strtolower($type) . '\\' . ucfirst($type);
        $get = $book_model::whereIn('id',$categories) -> limit($limit);
        if (stripos($order,'(') !== false) {
            $get -> orderRaw($order);
//            $get = Cate::where($where)->with([ucfirst($type) => function($query) use($limit) {
//                $query -> limit(1)->orderRaw('rand()');
//            }])->select();
        } else {
            $get -> order($order);
//            $get = Cate::where($where)->with([ucfirst($type) => function($query) use($limit,$order) {
//                $query -> limit($limit)->order($order);
//            }])->limit($limit)->select();
        }
        $out = $get -> select();// -> fetchSql(1)
//halt($out);
//        if ($raw)
//            return $get;

//        trace(\collection($get) -> toArray());

//        $out = [];
//        foreach ($get as $item) {
//            foreach ($item[$type] as $book) {
//                $out[$book['id']] = $book;
//            }
//        }
//        shuffle($out);

//        trace($out);
        return $out;
    }

    /**
     * 获取书本分类ID
     * @param $id
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBookCate($id, $type)
    {
        $detail = BookService::getBookDetail($id, $type);
        $cate = [];
        foreach ($detail['cate'] as $item) {
            if (!is_null($item['id']))
                $cate[] = $item['id'];
        }
        return $cate;
    }

    /**
     * 通过书本详细直接获取分类
     * @param Model|array $detail 书本模型或者数组
     * @return array
     */
    public static function getBookCateByDetail($detail)
    {
        $cate = [];
        foreach ($detail['cate'] as $item) {
            if (!is_null($item['id']))
                $cate[] = $item['id'];
        }
        return $cate;
    }

    /**
     * 全文检索书本
     * 包括书名和介绍
     * @param string $word
     * @param string $type
     * @param int|string $limit
     * @param bool|string $order
     * @param bool $paginate
     * @param int $page_size
     * @param array $page_config
     * @return null|array
     */
    public static function search($word, $type = 'cartoon', $limit = 15, $order = false, $paginate = false, $page_size = 15, $page_config = [])
    {
        if (($type = check_type($type)) === false)
            return null;
        $model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
        $obj = $model::where('match(`name`,`summary`) against( "' . $word . '" IN NATURAL LANGUAGE MODE)') -> with('author');
        if ($order) {
            $obj->order($order);
        }
        if ($limit) {
            $obj->limit($limit);
        }
        if ($paginate !== false) {
            $list = $obj->paginate($page_size, false, $page_config);
        } else {
            $list = $obj->select();
        }
//        halt(Cartoon::getLastSql());
        return $list;
    }
}