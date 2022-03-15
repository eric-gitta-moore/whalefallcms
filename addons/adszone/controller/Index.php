<?php

namespace addons\adszone\controller;

use addons\adszone\library\Adszone;
use think\addons\Controller;
use Exception;
use think\Request;

/**
 *
 */
class Index extends \think\addons\Controller
{
    protected $layout = 'default';
    protected $config = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->view->assign("title", "广告位调用演示DEMO");
    }

    /**
     *
     */
    public function index()
    {
        //$this->layout = null;
        //$this->view->engine->layout(false); //控制layout是否生效
        $adsConfig = get_addon_config('adszone');
        if ($adsConfig['developerTips'] == 0) {
            $this->error("当前插件暂无前台页面");
        }
        $Adszone = new Adszone(); //
        //$ads_zone_01 = $Adszone->getAdsById(1); //根据广告位ID获取广告位
        $ads_slide_01 = $Adszone->getAdsByMark("slide_01");//根据广告位标记获取广告位
        $ads_slide_02 = $Adszone->getAdsByMark("slide_02");//根据广告位标记获取广告位
        $ads_imgTable_01 = $Adszone->getAdsByMark("imgTable_01");//根据广告位标记获取广告位

        trace($ads_slide_01);


        $ads_image_01 = $Adszone->getAdsByMark("image_01");//根据广告位标记获取广告位
        $ads_adsCode_01 = $Adszone->getAdsByMark("adsCode_01");//根据广告位标记获取广告位
        $ads_zone_01 = $Adszone->getAdsByMark("slide_01");//根据广告位标记获取广告位
		
		
		$request = Request::instance();
		$rootUrl = dirname($request->baseFile());
		$rootUrl=$rootUrl=="\\"?"":$rootUrl;
		$this->view->assign('rootUrl', $rootUrl);
        $this->view->assign('ads_slide_01', $ads_slide_01);
        $this->view->assign('ads_slide_02', $ads_slide_02);
        $this->view->assign('ads_imgTable_01', $ads_imgTable_01);
        $this->view->assign('ads_image_01', $ads_image_01);
        $this->view->assign('ads_adsCode_01', $ads_adsCode_01);
        //$this->view->assign('ads_zone_01', $ads_zone_01);
        return $this->view->fetch();
    }
}
