<?php

namespace addons\user\controller;

use think\addons\Controller;

/**
 * 会员扩展
 */
class Index extends Controller
{

    public function index()
    {
        $this->error("当前插件暂无前台页面,即将进入用户端！", url("/user/index/index"));
    }

}
