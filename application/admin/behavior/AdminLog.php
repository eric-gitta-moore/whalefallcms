<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\behavior;

class AdminLog
{
    public function run(&$params)
    {
        if (request()->isPost()) {
            \app\admin\model\AdminLog::record();
        }
    }
}
