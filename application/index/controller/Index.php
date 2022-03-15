<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\index\controller;

use app\common\controller\BaseFrontend;
use app\service\BannerService;
use app\common\model\config\Links;
use app\service\cartoon\CartoonService;
use app\service\BookService;
use app\service\SubnavService;
use think\View;

class Index extends BaseFrontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public $module;

    public function _initialize()
    {

        $arr = [
            'index' => 'cartoon',
            'cartoon' => 'cartoon',
            'novel' => 'novel',
            'listen' => 'listen',
        ];

        $this->module = $arr[strtolower($this->request -> action())];


        parent::_initialize();


        //获取轮播图
        $Adszone = new \addons\adszone\library\Adszone();
        $ads = $Adszone->getAdsByMark($this->module . '_index_images'); //按照标记调用广告位
        trace($ads);

        //友链
        $friend_links = Links::where('status','>','0') -> order(['status' => 'desc','updatetime' => 'desc']) -> select();

        //子导航
        $subnav = SubnavService::getNav($this->module);

        View::share([
            'banners' => $ads,
            'friend_links' => $friend_links,
            'subnav' => $subnav,
        ]);
    }

    public function index()
    {

        //最近更新
        $news = BookService::getNews($this->module,true);

        //热门推荐
        $goods =  BookService::getGoodsGroupByCate('portal',$this->module);;

        //实际购买量最高的漫画
        $charge =  BookService::getBooks($this->module,'1=1','1=1','chargenum desc',9);;

        //免费书籍
        $free_books = [];
        $is_show_free_books = BookService::getPageConfig('is_show_portal_free_'.$this->module,$this->module,'switch');
        if ($is_show_free_books)
        {
            $free_books = BookService::getBooks($this->module,['free_type' => 0],'','log_time desc');
            $free_books = load_relation($free_books,'Author');
        }

//        trace($news);
//        halt(collection($charge) -> toArray());

        $this->view -> assign([
            'news' => $news,
            'charge' => $charge,
            'goods' => $goods,
            'free_books' => $free_books,
        ]);

        return view();
    }

    public function cartoon()
    {
        $this->request -> action('index');
        return $this->index();
    }

    public function novel()
    {
        $this->request -> action('index');
        return $this->index();
    }

    public function listen()
    {
        $this->request -> action('index');
        return $this->index();
    }

}
