<?php
/**
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:08
*/
namespace addons\qnbuygroup\controller;

use think\addons\Controller;

class Index extends Controller
{

    public function index()
    {
        $this->redirect('index/qnbuygroup.buygroup/buy');
    }

}

