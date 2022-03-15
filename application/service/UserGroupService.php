<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


class UserGroupService
{
    public static function getVipGroups()
    {
        return [
            'cartoon' => config('site.group_cartoon_vip_id'),
            'novel' => config('site.group_novel_vip_id'),
            'listen' => config('site.group_listen_vip_id'),
            'all' => config('site.group_all_vip_id')
        ];
    }
}