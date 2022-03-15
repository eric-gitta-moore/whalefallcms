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
use app\service\UserService;

abstract class Book extends BaseFrontend
{

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'detail',

    ];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [
        '*'
    ];

    /**
     * @var string 清空布局
     */
    protected $layout = '';

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

    public function detail($id)
    {
        $detail = BookService::getBookDetail($id,$this->module);
//        halt($detail);
        if (is_null($detail) || (is_array($detail) && count($detail) == 1 && array_key_exists('reward_count',$detail) ) )
        {
            return view('common/error',[
                'msg' => '书库里没有这本书',
                'url' => url('index/index/index')
            ]);
//            $this->error('书库里没有这本书',url('index/index/index'));
        }

        $collection = false;
        if ($this->auth -> getUser())
            $collection = UserService::getUserCollection($this->auth -> getUser() -> id,$id,$this->module);

        $cate = [];
        foreach ($detail['cate'] as $item) {
            if (!is_null($item['id']))
                $cate[] = $item['id'];
        }
        $hots = BookService::getRandomBooks($this->module,$cate,6);

        $user_buy_log = [];
        if ($this->auth -> getUser())
            $user_buy_log = UserService::getUserBuyBookStatus($this->auth -> getUser() -> id,$id,false,$this->module,true);


        trace($detail -> toArray());
//        trace($collection);
//        trace(collection($hots) -> toArray());
        trace(collection($user_buy_log) -> toArray());
//        trace(config('site.gift_images'));
//        trace(config('site.reward_gift'));
        $this->bookName = $detail['name'];
        $this->bookLastChapter = $detail['last_chapter'];
        $this->bookSummary = $detail['summary'];
        $this->bookCate = !empty($detail['cate'])?array_column($detail['cate'],'name'):[];
        $this->bookAuthorName = $detail['author_name'];

        $this->renderSeo();

        return view('',[
            'detail' => $detail,
            'collection' => $collection,
            'hots' => $hots,
            'user_buy_log' => (array)$user_buy_log,
        ]);
    }

    protected function renderSeo()
    {
        $base_key_name = 'site.' . $this->module . '_book' . '_seo_';
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

        if (stripos($field,'{book_cate,') !== false)
        {
            preg_match('/{book_cate,([^}]+)}/i',$field,$matches);
            $delimiter = $matches[1];
//            halt($delimiter);
            $field = str_replace('{book_cate,' . $delimiter . '}',implode($delimiter,$this->bookCate),$field);
        }
        if (stripos($field,'{book_summary,') !== false)
        {
            preg_match('/{book_summary,(\d+)}/i',$field,$matches);
            $limit = $matches[1];
//            halt(mb_strcut($this->bookSummary,0,$limit));
            $field = str_replace('{book_summary,' . $limit . '}',mb_strcut($this->bookSummary,0,$limit),$field);
        }
        return $field;

    }


}















