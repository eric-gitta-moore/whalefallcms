<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\index\controller;


use app\common\controller\BaseFrontend;
use app\common\model\config\Cate;
use app\service\BookService;
use app\service\UserGroupService;
use app\service\UserService;
use think\Model;

abstract class Chapter extends BaseFrontend
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'show'
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

    private $bookName = '';
    private $bookAuthorName = '';
    private $bookLastChapter = '';
    private $bookCate = [];
    private $bookSummary = '';

    private $chapterName = '';

    public function show($id)
    {
//        in_array()
        $model = '\app\common\model\\' . $this->module;

        $book_model = $model . '\\' . ucfirst($this->module);
        $book_primary_key = $this->module . '_' . $this->module . '_id';

        $chapter_model = $model . '\Chapter';
        if ($this->module == 'cartoon') {
            $chapter = $chapter_model::with('photos')->find($id);
        } else {
            $chapter = $chapter_model::find($id);
        }
        $chapter['book'] = BookService::getBookDetail($chapter[$book_primary_key], $this->module);
//        halt($chapter);
//        if (is_object($chapter))
        if (is_array($chapter)) {
            if (isset($chapter['book']['reward_count']) && count($chapter) == 1) {
                return view('common/error', [
                    'msg' => '没有该章节哦',
                    'url' => '/'
                ]);
            }
        }
        $chapter = $chapter->toArray();

        //处理付费章节
        /*
         * 如果书本free_type是收费的，其它收费设置才生效
         *
         */
        $book = &$chapter['book'];
        $vip_group_set = UserGroupService::getVipGroups();

        //判断该章节是否为付费章节
        $current_chapter_order = $chapter_model::where([
            $book_primary_key => $book['id'],
            'weigh' => ['<=', $chapter['weigh']]
        ])-> order('weigh')->count();
//        trace($current_chapter_order);
//        halt($current_chapter_order);
        $need_pay = false;
        $payed = false;
        $is_show_tips = false;
        $tips_msg = false;
        $append_html = '';
        do {
            //收费类型:0=免费,1=收费
            if ($book['free_type'] == 0)
                break;

            //当前章节数小于起始付费章节数，则跳过
            if ($current_chapter_order < $book['start_pay'])
                break;

            //略过VIP
            //vip_type:VIP全免费:0=否,1=是
            //这里排除至尊VIP
            if ($book['vip_type'] == 0 && !is_null($this->auth))//如果VIp也不是免费的
            {
                $current_vip_group_id = $vip_group_set[$this->module];
                $user = $this->auth->getUser();
//                        //普通VIP
//                        if (in_array($user -> group_id,[$current_vip_group_id] ) )
//                        {
//                            //普通VIP依旧删减
//                        }
                //至尊VIP
                if ($user->group_id == $vip_group_set['all']) {
                    //免费
                    //不删减
                    $is_show_tips = true;
                    $tips_msg = '您是尊贵的VIP会员<br />已经为您自动显示全部内容';
                    break;
                }

            }

            //判断购买情况
            if (!is_null($this->auth))
            {
                $user = $this->auth->getUser();
                $is_show_tips = true;


//                trace($chapter);
                //判断是否购买
                $buylog = UserService::getUserBuyBookStatus($user -> id,$chapter[$book_primary_key],$chapter['id'],$this->module,true);
//                trace($buylog);
                if (!empty($buylog))
                {
                    //已购
                    $tips_msg = '您已购买<br />已经为您自动显示全部内容';
                    $payed = true;
                    break;
                }
                else
                {
                    //未购

                    //判断自动购买
                    $auto_pay = session('auto_pay');
                    if ($auto_pay)
                    {
                        $tips_msg = UserService::buyChapter($chapter['id'],$user,$this->module);
                    }
                    if ($tips_msg === true)
                    {
                        $tips_msg = '已为您自动购买本章节' . '<br />当前' . config('site.score_name') . ':' . strval(intval($user -> score) - intval($chapter['money']));
                        break;
                    }

//                    $payed = true;
                }

            }

            //删减图片
            if ($this->module == 'cartoon')
            {
                $append_html = config('site.cartoon_need_pay_show_html');
                $chapter['photos'] = [$chapter['photos'][0]];
            }
            elseif ($this->module == 'novel')
            {
                $append_html = config('site.novel_need_pay_show_html');
                $chapter['content'] = mb_strcut($chapter['content'],0,config('site.novel_need_pay_cut_length') * 2) . '...';

            }


            $is_show_tips = true;
            $tips_msg = '您当前还未购买本章节哦<br />' . $tips_msg;
            $need_pay = true;
        } while (false);

//        trace($chapter instanceof Model);
        trace($chapter);
//        trace($chapter);

        $collection = null;
        $likes = null;
        if (!is_null($this->auth)) {
            $user = $this->auth->getUser();
            $collection = $user->collection()->where([
                'status' => $this->module,
                'bookid' => $chapter[$book_primary_key]
            ])->find();
            $likes_fun_name = $this->module . 'Likes';
            $likes = $user->$likes_fun_name()->where([
                $book_primary_key => $chapter[$book_primary_key]
            ])->count();
        }

//        if (!is_null($collection))
//            trace($collection->toArray());

        //上一章节，下一章节
        $previous_chapter = $chapter_model::where([
            $book_primary_key => $book['id'],
            'id' => ['not in', $chapter['id']],
            'weigh' => ['<=', $chapter['weigh']]
        ])->order('weigh')->find();
        if (!is_null($previous_chapter))
            $previous_url = url('index/' . $this->module . '.chapter/show', ['id' => $previous_chapter['id']]);
        else
            $previous_url = '';

        $next_chapter = $chapter_model::where([
            $book_primary_key => $book['id'],
            'id' => ['not in', $chapter['id']],
            'weigh' => ['>=', $chapter['weigh']]
        ])->order('weigh')->find();
        if (!is_null($next_chapter))
            $next_url = url('index/' . $this->module . '.chapter/show', ['id' => $next_chapter['id']]);
        else
            $next_url = '';

        if (!is_null($previous_chapter))
            trace($previous_chapter -> toArray());
        if (!is_null($next_chapter))
            trace($next_chapter -> toArray());
        trace($previous_url);
        trace($next_url);

        $hots = BookService::getRandomBooks($this->module, BookService::getBookCateByDetail($book));
//        trace(count($hots));
        $hots_extend = [];
        if (count($hots) < 6)
        {
            $hots_extend = BookService::getRandomBooks($this->module,[],6-count($hots));
        }
        $hots = array_merge($hots,$hots_extend);

        //更新浏览量
        if (config('site.put_off_update_view_number'))
        {
            $r = $book_model::get($chapter[$book_primary_key]) -> setInc('readnum',1,60);
        }
        else
        {
            $r = $book_model::get($chapter[$book_primary_key]) -> setInc('readnum');
        }
        trace($r);

        trace('------------------------');

        trace([
            'chapter' => $chapter,
            'collection' => $collection,
            'likes' => $likes,
            'previous_url' => $previous_url,
            'next_url' => $next_url,
            'hots' => $hots,
            //兼容打赏模块
            'detail' => $book,
            'need_pay' => $need_pay,
            'tips_msg' => $tips_msg,
            'payed' => $payed,
            'is_show_tips' => $is_show_tips,
            'user_info' => !is_null($this->auth)?$this->auth -> getUserinfo():[],
            'auto_pay' => session('auto_pay'),
            'append_html' => $append_html,
        ]);

//        trace($hots);

        $detail = $chapter['book'];
        $this->bookName = $detail['name'];
        $this->bookLastChapter = $detail['last_chapter'];
        $this->bookSummary = $detail['summary'];
        $this->bookCate = !empty($detail['cate'])?array_column($detail['cate'],'name'):[];
        $this->bookAuthorName = $detail['author_name'];
        $this->chapterName = $chapter['name'];

        $this->renderSeo();

        return view('', [
            'chapter' => $chapter,
            'collection' => $collection,
            'likes' => $likes,
            'previous_url' => $previous_url,
            'next_url' => $next_url,
            'hots' => $hots,
            //兼容打赏模块
            'detail' => $book,
            'need_pay' => $need_pay,
            'tips_msg' => $tips_msg,
            'payed' => $payed,
            'is_show_tips' => $is_show_tips,
            'user_info' => !is_null($this->auth)?$this->auth -> getUserinfo():[],
            'auto_pay' => session('auto_pay'),
            'append_html' => $append_html,
        ]);
    }

    protected function renderSeo()
    {
        $base_key_name = 'site.' . $this->module . '_chapter' . '_seo_';
        $title_key_name = $base_key_name . 'title';
        $keywords_key_name = $base_key_name . 'keywords';
        $description_key_name = $base_key_name . 'description';
        if (config('?' . $title_key_name))
        {
            $this->view -> title = $this->parseSeoField(config($title_key_name));
        }
        if (config('?' . $keywords_key_name))
        {
            $this->view -> keywords = $this->parseSeoField(config($keywords_key_name));
        }
        if (config('?' . $description_key_name))
        {
            $this->view -> description = $this->parseSeoField(config($description_key_name));
        }
    }

    protected function parseSeoField($field)
    {
        $field = str_replace('{site_name}',config('site.name'),$field);
        $field = str_replace('{book_name}',$this->bookName,$field);
        $field = str_replace('{book_author_name}',$this->bookAuthorName,$field);
        $field = str_replace('{book_last_chapter}',$this->bookLastChapter,$field);
        $field = str_replace('{chapter_name}',$this->chapterName,$field);

        if (stripos($field,'{book_cate,') !== false)
        {
            preg_match('/{book_cate,([^}]+)}/i',$field,$matches);
            $delimiter = $matches[1];
            $field = str_replace('{book_cate,' . $delimiter . '}',implode($delimiter,$this->bookCate),$field);
        }
        if (stripos($field,'{book_summary,') !== false)
        {
            preg_match('/{book_summary,(\d+)}/i',$field,$matches);
            $limit = $matches[1];
            $field = str_replace('{book_summary,' . $limit . '}',mb_strcut($this->bookSummary,0,$limit),$field);
        }
        return $field;

    }
}