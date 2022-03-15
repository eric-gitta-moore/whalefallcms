<?php
/**
 * 云凌鲸落小说漫画聚合分销CMS系统
 * @Author Curtis - 云凌工作室
 * @Website http://www.whalefallcms.com
 * @Datetime 2020/4/8 下午 05:07
 */


namespace app\api\controller;


use app\common\controller\Api;
use app\common\model\cartoon\Photos;
use app\common\model\config\Cate;
use Redis;
use think\Cache;
use think\Request;

class PostBot extends Api
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = ['*'];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    /**
     * @var array 完结状态映射
     */
    protected $end_map = [
        '连载' => 0,
        '连载中' => 0,
        '完结' => 1,
        '已完结' => 1,
    ];

    /**
     * Redis实例
     * @var Redis
     */
    protected $redis_handle;

    /**
     * 当多线程进入临界区失败时重试次数
     * @var int
     */
    protected $onFailedRetryTimes = 5;

    /**
     * Redis加锁过期时间
     * @var int
     */
    protected $lockedKeyTTL = 5;

    /**
     * @var array 过滤后参数
     */
    protected $allData;

    /**
     * @var array 书本参数过滤
     */
    protected $book_fitter = [
        'book_name',                 //书本名,必须
        'book_status',               //书本完结状态:0=连载,1=完结,默认0
        'book_cover_image',          //竖着的封面,必须
        'book_horizon_image',        //横着的封面
        'book_big_image',            //高清大图
        'book_log_time',             //最后更新时间,默认发布时间
        'book_summary',              //作品简介
        'book_switch',               //书本上架状态:0=下架,1=上架,2=预留字段,3=预留字段,默认1
        'book_free_type',            //收费类型:0=免费,1=收费,默认0
        'book_vip_type',             //VIP全免费:0=否,1=是,默认1
        'book_start_pay',            //第n话开始需要付费,默认3
        'book_readnum',              //阅读数,默认随机1000到9999
        'book_collectnum',           //收藏数,默认随机1000到9999
        'book_likenum',              //点赞数,默认随机1000到9999
        'book_new_switch',           //是否最近更新:0=否,1=是,默认1
        'book_recommend_switch',     //是否精选推荐:0=否,1=是,默认1
        'book_chargenum',            //购买数,默认随机1000到9999
    ];

    /**
     * @var array 作者参数过滤
     */
    protected $author_fitter = [
        'author_name',                 //作者名称,默认:佚名
    ];

    /**
     * @var array 章节参数过滤
     */
    protected $chapter_fitter = [
        'chapter_weigh',                //排序,默认0
        'chapter_log_time',             //最后更新时间,默认NULL
        'chapter_status',               //章节上架状态:0=下架,1=上架,默认1
        'chapter_money',                //本章节所需费用,默认0
        'chapter_likenum',              //本章节点赞数,默认随机1000到9999
        'chapter_readnum',              //本章节阅读数,默认随机1000到9999
        'chapter_name',                 //章节名,必须
        'chapter_image',                //章节头图片,默认NULL
    ];

    /**
     * @var array 分类参数过滤
     */
    protected $cate_fitter = [
        'cate_name'                     //分类名称,格式:分类1,分类2    或    分类1 分类2    或    分类1|分类2
    ];

    /**
     * @var string 当前采集类型,cartoon:漫画,novel:小说,listen:听书
     */
    protected $type = 'cartoon';

    /**
     * @var string 书本外键
     */
    protected $book_foreign_key;

    /**
     * @var string 作者外键
     */
    protected $author_foreign_key;

    /**
     * @var string 章节外键
     */
    protected $chapter_foreign_key;

    /**
     * @var string 书本模型命名空间
     */
    protected $book_model_namespace;

    /**
     * 章节模型命名空间
     * @var string
     */
    protected $chapter_model_namespace;

    /**
     * @var string 作者模型命名空间
     */
    protected $author_model_namespace;

    protected function _initialize()
    {
        parent::_initialize();
        if (input('api_key') != config('site.api_postbot_pwd')) {
            $this->error('接口密匙错误');
        }

        $this->redis_handle = Cache::store('redis')->handler();

        if ($type = check_type(strtolower($this->request->action())))
            $this->type = $type;
        else
            $this->type = check_type(input('type'));
        if (empty($this->type))
            $this->error('请指定类型');
//        halt($this->type);

        $base_namespace = '\app\common\model\\' . $this->type . '\\';
        $this->book_model_namespace = $base_namespace . ucfirst($this->type);
        $this->author_model_namespace = $base_namespace . 'Author';
        $this->chapter_model_namespace = $base_namespace . 'Chapter';

        $this->book_foreign_key = $this->type . '_' . $this->type . '_id';
        $this->author_foreign_key = $this->type . '_author_id';
        $this->chapter_foreign_key = $this->type . '_chapter_id';
    }

    public function test()
    {
//        session('a');
        var_dump(config('cache'));
    }

    protected function initParams()
    {
        $book_fitter = $this->book_fitter;
        $author_fitter = $this->author_fitter;
        $chapter_fitter = $this->chapter_fitter;
        $cate_fitter = $this->cate_fitter;

        if ($this->type == 'cartoon') {
            $photos_fitter = [
                'photos_images'                 //图片集,必须,可为 html标签 或者 英文逗号分隔的链接组
            ];
        }

        //参数过滤
        Request::instance()->filter('trim');
        $data['book'] = request()->only($book_fitter);
        $data['author'] = request()->only($author_fitter);
        $data['chapter'] = request()->only($chapter_fitter);
        $data['cate'] = request()->only($cate_fitter);
        if ($this->type == 'cartoon') {
            $data['photos'] = request()->only($photos_fitter);
        }

        //过滤火车头的空参数
        foreach ($data as &$datum) {
            foreach ($datum as &$item) {
                if (stripos($item, '[db:') === 0)
                    $item = null;
            }
            $datum = $this->unFitterArrayKeys($datum);
        }

        if (empty($data['book']['name']))
            $this->error('书本名称不能为空');
        if (empty($data['chapter']['name']))
            $this->error('章节名称不能为空');
        $data['book']['last_chapter'] = $data['chapter']['name'];//halt($data['book']['last_chapter']);

        //检查图片
        $general_image = '';
        if (!empty($data['book']['cover_image']))
            $general_image = $data['book']['cover_image'];
        else
            if (!empty($data['book']['big_image']))
                $general_image = $data['book']['big_image'];
            else
                if (!empty($data['book']['horizon_image']))
                    $general_image = $data['book']['horizon_image'];

        if (empty($general_image))
            $this->error('至少上传一张图片');

        if (empty($data['chapter']['image']))
            $data['chapter']['image'] = $general_image;
        if (empty($data['book']['horizon_image']))
            $data['book']['horizon_image'] = $general_image;
        if (empty($data['book']['big_image']))
            $data['book']['big_image'] = $general_image;
        if (empty($data['book']['cover_image']))
            $data['book']['cover_image'] = $general_image;

        //检查整型字段
        $data['book']['switch'] = intval(isset($data['book']['switch']) ? $data['book']['switch'] : 1);
        $data['book']['free_type'] = intval(isset($data['book']['free_type']) ? $data['book']['free_type'] : 0);
        $data['book']['vip_type'] = intval(isset($data['book']['vip_type']) ? $data['book']['vip_type'] : 1);
        $data['book']['start_pay'] = intval(isset($data['book']['start_pay']) ? $data['book']['start_pay'] : 3);
        $data['book']['readnum'] = intval(isset($data['book']['readnum']) ? $data['book']['readnum'] : rand(1000, 9999));
        $data['book']['collectnum'] = intval(isset($data['book']['collectnum']) ? $data['book']['collectnum'] : rand(1000, 9999));
        $data['book']['likenum'] = intval(isset($data['book']['likenum']) ? $data['book']['likenum'] : rand(1000, 9999));
        $data['book']['new_switch'] = intval(isset($data['book']['new_switch']) ? $data['book']['new_switch'] : 1);
        $data['book']['recommend_switch'] = intval(isset($data['book']['recommend_switch']) ? $data['book']['recommend_switch'] : 1);
        $data['book']['chargenum'] = intval(isset($data['book']['chargenum']) ? $data['book']['chargenum'] : rand(1000, 9999));

        $data['chapter']['weigh'] = intval(isset($data['chapter']['weigh']) ? $data['chapter']['weigh'] : 0);
        $data['chapter']['status'] = intval(isset($data['chapter']['status']) ? $data['chapter']['status'] : 1);
        $data['chapter']['money'] = intval(isset($data['chapter']['money']) ? $data['chapter']['money'] : 0);
        $data['chapter']['likenum'] = intval(isset($data['chapter']['likenum']) ? $data['chapter']['likenum'] : rand(1000, 9999));
        $data['chapter']['readnum'] = intval(isset($data['chapter']['readnum']) ? $data['chapter']['readnum'] : rand(1000, 9999));


        //检查时间
        if (empty($data['book']['log_time']))
            $data['book']['log_time'] = time();
        if (empty($data['chapter']['log_time']))
            $data['chapter']['log_time'] = time();

        $data['chapter']['createtime'] = time();
        $data['chapter']['updatetime'] = time();
        $data['book']['createtime'] = time();
        $data['book']['updatetime'] = time();

        //检查字段
        if (!isset($data['book']['start_pay']))
            $data['book']['start_pay'] = '3';

        //转换完结状态
        if (isset($data['book']['status'])) {
            $data['book']['status'] = $this->parseEndType($data['book']['status']);
        }

//        halt(input('photos_images'));
//        halt($this->type);
        if ($this->type == 'cartoon') {
            if (empty($data['photos']['images']))
                $this->error('章节内容图片不能为空');
            else {
                $data['photos']['images'] = $this->parseImages($data['photos']['images']);
            }
        }

        //作者
        if (empty($data['author']['name']))
            $data['author']['name'] = '佚名';

        //赋值成员变量
        $this->allData = $data;
        //参数初始化完成----------------------------------------------------------
    }

    protected function commonWrite()
    {
        $data = $this->allData;

//        Db::startTrans();echo 'startTrans';

        //写入作者
        $author_id = $this->createAuthor($data['author']['name'], $this->type);
//        var_dump($author_id);
        if (is_null($author_id))
            $this->error('作者写入失败');
        $data['book'][$this->author_foreign_key] = $author_id;

        $has = false;
//        $book_model = [];
        $book_id = -1;
        $find_book = $this->book_model_namespace::get(['name' => $data['book']['name'], 'author_name' => $data['author']['name']]);
//        halt($find_book);
        if (!is_null($find_book)) {
//            $book_model = $find_book;
            $book_id = $find_book->id;
            $has = true;
        }

        //写入书本
        if ($has == false) {
            //进入临界区
            $redis_lock_key = md5('book' . $data['book']['name'] . $data['author']['name']);
            $flag = $this->redis_handle->setnx($redis_lock_key, 1);
            if ($flag) {
                //写入成功，该线程为第一个进入的线程

                //设置过期
                $this->redis_handle->expire($redis_lock_key, $this->lockedKeyTTL);

                //补充参数
                $data['book']['author_name'] = $data['author']['name'];
                $book_model = new $this->book_model_namespace();
                $r = $book_model->allowField(true)->save($data['book']);
//                halt($r);
                if (!$r) {
                    $this->error('书本写入失败');
                }
                $book_id = $book_model->id;
//                $book_model = Cartoon::get($r);

            } else {
                //写入失败，其它线程在此，等待第一个线程写入数据库

                //尝试获取书本模型
                for ($cnt = 0; $cnt < $this->onFailedRetryTimes; $cnt++) {
                    sleep(0.3);
                    if ($find_book = $this->book_model_namespace::get(['name' => $data['book']['name'], 'author_name' => $data['author']['name']])) {
                        $book_id = $find_book->id;
                        break;
                    }
                }

                //判断是否获取成功
                if ($book_id <= 0) {
                    $this->error('线程获取书本模型失败');
                }

            }
        }

        //更新书本最新章节
        $last_chapter = $this->chapter_model_namespace::where([$this->book_foreign_key => $book_id])->order('weigh desc')->value('name');
//        halt($last_chapter);
//        halt($this->book_model_namespace);
//        $book_model_namespace = $this->book_model_namespace;
        (new $this->book_model_namespace) ->  save(['last_chapter' => $last_chapter],['id' => $book_id]);//halt($r);
//        halt($find_book -> toArray());
//        $r = $find_book -> save(['last_chapter' => $last_chapter]);//halt($r);
//        if (!$r)
//            $this->error('更新书本最新章节失败');

        //写入分类
        if (!empty($data['cate']))
            $this->createCategory($data['cate']['name'], $book_id, $this->type);

        //写入章节
        $chapter = $this->chapter_model_namespace::get([
            'name' => $data['chapter']['name'],
            $this->book_foreign_key => $book_id
        ]);
//        halt($chapter);
        if (is_null($chapter)) {
            $data['chapter'][$this->book_foreign_key] = $book_id;
            $chapter_id = (new $this->chapter_model_namespace)->allowField(true)->insert($data['chapter'], false, true);
            $chapter = $this->chapter_model_namespace::get($chapter_id);
        }
        if (is_null($chapter)) {
            $this->error('章节写入失败');
        }
        $chapter_id = $chapter->id;

        return [$book_id, $chapter_id];
//        return ['book_id' => $book_id, 'chapter_id' => $chapter_id];
    }

    public function save()
    {
        $type = $this->type;
//        halt($type);
        $this->$type();
    }

    /**
     * 火车头发布漫画接口
     * @throws \think\exception\DbException
     */
    public function cartoon()
    {
        $this->initParams();
        $data = $this->allData;
        list($book_id, $chapter_id) = $this->commonWrite();

        if (is_null(Photos::get(['cartoon_chapter_id' => $book_id]))) {
            //写入图片
            $image_list = [];
            $time = time();
            foreach ($data['photos']['images'] as $key => $value) {
                $image_list[$key]['weigh'] = $key;
                $image_list[$key]['cartoon_chapter_id'] = $chapter_id;
                $image_list[$key]['pic_url'] = $value;
                $image_list[$key]['createtime'] = $time;
                $image_list[$key]['updatetime'] = $time;
            }
            $r = Photos::insertAll($image_list);
            if ($r <= 0)
                $this->error('写入图片失败');
        }


        $this->succ($book_id, $chapter_id);
    }

    /**
     * 火车头发布小说接口
     */
    public function novel()
    {
        $this->chapter_fitter[] = 'chapter_content';

        $this->initParams();
        $data = $this->allData;
        if (empty($data['chapter']['content']))
            $this->error('小说章节内容不能为空');

        list($book_id, $chapter_id) = $this->commonWrite();


        $this->succ($book_id, $chapter_id);
    }

    /**
     * 成功回显
     * @param int $book_id
     * @param int $chapter_id
     */
    protected function succ($book_id, $chapter_id)
    {
        $this->success('成功' . (!is_null($book_id) ? ';书本id:' . $book_id : '') . (!is_null($chapter_id) ? ';章节id:' . $chapter_id : ''));
    }

    /**
     * 创建分类并关联书本
     * @param $cate
     * @param $book_id
     * @param string $type
     * @throws \think\exception\DbException
     */
    protected function createCategory($cate, $book_id, $type = 'cartoon')
    {
        if ($book_id <= 0)
            $this->error('书本ID错误');
        if (empty($cate))
            return;

        $cate = str_replace(' ', ',', $cate);
        $cate = str_replace('|', ',', $cate);
        $cate = explode(',', $cate);
        foreach ($cate as $item) {
            $has_cate = Cate::get(['name' => $item, 'status' => $type]);
            $cate_id = 0;
            if (is_null($has_cate)) {
                $cate_id = Cate::insert(['name' => $item, 'status' => $type, 'createtime' => time(), 'updatetime' => time()], false, true);
            } else {
                $cate_id = $has_cate->id;
            }
            if ($cate_id) {
                //添加 书本ID-分类ID 关系表
                $relation = '\app\common\model\\' . $type . '\\Relation';
                $book_key_name = $type . '_' . $type . '_id';

                //判断关系是否存在
                $r = $relation::get([$book_key_name => $book_id, 'config_cate_id' => $cate_id]);
                if (!$r) {
                    $r = $relation::insert([$book_key_name => $book_id, 'config_cate_id' => $cate_id]);
                    if (!$r)
                        $this->error('书本ID-分类ID 关系表写入失败');
                }
            } else {
                $this->error('分类写入失败');
            }
        }
    }

    /**
     * 创建用户
     * @param string $name
     * @param string $type
     * @return null|int
     */
    protected function createAuthor($name, $type = 'cartoon')
    {
        $model = '\app\common\model\\' . $type . '\\Author';
//        $has = false;
        if (empty($name))
            return null;

        $author = $model::get(['name' => $name]);
        if (!is_null($author))
            return $author->id;
        else {
            $locked_key = md5('author' . $name);
            $area = $this->redis_handle->setnx($locked_key, 1);
            if ($area) {
                $this->redis_handle->expire($locked_key, $this->lockedKeyTTL);
                $id = $model::insert(['name' => $name, 'createtime' => time(), 'updatetime' => time()], false, true);
                return $id;
//                return $model::get($id);
            } else {
                for ($cnt = 0; $cnt < $this->onFailedRetryTimes; $cnt++) {
                    sleep(0.3);
                    $author = $model::get(['name' => $name]);
                    if (!is_null($author)) {
                        return $author->id;
                    }
                }
                return null;
            }
        }
    }

    /**
     * 解析文本中的图片链接
     * @param string $img
     * @return array
     */
    protected function parseImages($img)
    {
        if (!is_string($img))
            return [];

        if (stripos($img, '<img') !== false) {
            $preg = '/(?:\bsrc\b\s*=\s*)?[\'"]([^\'\"]{5,})[\'"]/i';
            preg_match_all($preg, $img, $img_urls);
//            halt($img_urls);
            return $img_urls[1];
        } elseif (stripos($img, ',') !== false) {
            $list = explode(',', $img);
            $list = array_map(function ($o) {
                return trim(trim(trim($o), '"'), "'");
            }, $list);
            return $list;
        } else
            return [];
    }

    /**
     * 解析漫画状态
     * @param $status
     * @return bool|string
     */
    protected function parseEndType($status)
    {
        if (strlen($status) != 1) {
            foreach ($this->end_map as $k => $v) {
                if (stripos($status, $k) !== false) {
                    return $v;
                }
            }
            return false;
        } else {
            return $status;
        }
    }

    /**
     * 去除索引数组 xxx_ 前缀
     * @param $array
     * @return array
     */
    protected function unFitterArrayKeys($array)
    {
        $out = [];
        foreach ($array as $key => $value) {
            $real_key = substr($key, stripos($key, '_') + 1);
            $out[$real_key] = $value;
        }
        return $out;
    }

}