<?php

namespace app\api\controller;

use app\common\controller\Api;

//use think\Config;

/**
 * 广告位管理
 *
 * @icon fa fa-circle-o
 */
class Adszone extends Api {

	// 无需登录的接口,*表示全部
	protected $noNeedLogin = '*';
	// 无需鉴权的接口,*表示全部
	protected $noNeedRight = '*';
	protected $AdszoneZone = null;
	protected $AdszoneAds = null;

	public function _initialize() {
		parent::_initialize();
		$this->AdszoneZone = new \addons\adszone\model\AdszoneZone();
		$this->AdszoneAds = new \addons\adszone\model\AdszoneAds();
	}

	/**
	 * 根据广告位ID获取广告位信息
	 *
	 * @ApiTitle    (根据广告位ID获取广告位信息)
	 * @ApiSummary  (根据广告位ID获取广告位信息)
	 * @ApiMethod   (GET)
	 * @ApiParams   (name="id", type="integer", required=true, description="广告位ID")
	 * @ApiReturn   ({
	  "code":1,
	  "msg":"返回成功",
	  "time":"1548058290",
	  "data":{
		"name":"广告位名称",
		"type":"1",//广告位类型   1：图片广告   2：多图&幻灯广告   3：代码广告
		"width":0,
		"height":0,
		"data":[
		  {
		  "title":"广告内容名称",
		  "imageurl":"广告图片地址",
		  "linkurl":"广告跳转地址",
		  "expiretime":2019698944,//广告内容到期时间
		  "weigh":0
		  }
		]
	  }
})
	 */
	public function getAdsById() {
		$id = intval($this->request->request('id'));
		if ($id <= 0) {
			$this->error(__('Invalid parameters'));
		}
		$Adszone = new \addons\adszone\library\Adszone();
		$result = $Adszone->getAdsById($id);
		//$result = array();
		if ($result) {
			$this->success('返回成功', $result);
		} else {
			$this->error("广告位不存在");
		}
	}

	/**
	 * 根据广告位标记获取广告位信息
	 *
	 * @ApiTitle    (根据广告位标记获取广告位信息)
	 * @ApiSummary  (根据广告位标记获取广告位信息)
	 * @ApiMethod   (GET)
	 * @ApiParams   (name="mark", type="string", required=true, description="广告位标记")
	 * @ApiReturn   ({
	  "code":1,
	  "msg":"返回成功",
	  "time":"1548058290",
	  "data":{
		"name":"广告位名称",
		"type":"1",//广告位类型   1：图片广告   2：多图&幻灯广告   3：代码广告
		"width":0,
		"height":0,
		"data":[
		  {
		  "title":"广告内容名称",
		  "imageurl":"广告图片地址",
		  "linkurl":"广告跳转地址",
		  "expiretime":2019698944,//广告内容到期时间
		  "weigh":0
		  }
		]
	  }
})
	 */
	public function getAdsByMark() {
		$mark = $this->request->request('mark');
		if ($mark == "") {
			$this->error(__('Invalid parameters'));
		}
		$Adszone = new \addons\adszone\library\Adszone();
		$result = $Adszone->getAdsByMark($mark);
		//$result = array();
		if ($result) {
			$this->success('返回成功', $result);
		} else {
			$this->error("广告位不存在");
		}
	}

}
