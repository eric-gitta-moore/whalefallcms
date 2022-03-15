<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


use app\common\model\config\Subnav;

class SubnavService
{
    public static function getNav($type = '',$switch = '1',$order = 'weigh desc',$cache=true)
    {
        if ($switch !== false)
        {
            $navs_ = Subnav::where('switch','in',$switch) -> order($order) -> cache($cache,config('site.query_cache')) -> select();
        }
        else
        {
            $navs_ = Subnav::where('1=1') -> order($order) -> cache($cache,config('site.query_cache')) -> select();
        }

        $navs = [
            'cartoon' => [],
            'novel' => [],
            'listen' => [],
        ];
        foreach ($navs_ as $nav) {
            $navs[$nav['category']][] = $nav;
        }
//        trace($navs);
        if ($type)
        {
            if ($navs[$type])
            {
                return $navs[$type];
            }
            else
            {
                return false;
            }
        }
        return $navs;
    }

}