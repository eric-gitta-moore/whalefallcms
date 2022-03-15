<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;

use app\common\model\config\Banner as BannerModel;

/**
 * 横幅服务类
 * Class Banner
 * @package app\service
 */
class BannerService
{
    public static function getBanners($type = '', $switch = '1', $order = 'weigh desc', $cache = true)
    {

        if ($switch !== false) {
            $banners = BannerModel::where('switch', 'in', $switch) -> order($order)->cache($cache, config('site.query_cache'))->select();
        } else {
            $banners = BannerModel::where('1=1') -> order($order)->cache($cache, config('site.query_cache'))->select();
        }
//        $category = new Category();
//        $category = $category -> getCate('banner');
//        trace($category['cartoon']['id']);
        $category_arr = [
            'cartoon' => [],
            'novel' => [],
            'listen' => [],
        ];
        foreach ($banners as $banner) {
            if ($banner['category'] == 'cartoon')
                $category_arr['cartoon'][] = $banner;
            if ($banner['category'] == 'novel')
                $category_arr['novel'][] = $banner;
            if ($banner['category'] == 'listen')
                $category_arr['listen'][] = $banner;
        }

        return !empty($type) ? $category_arr[$type] : $category_arr;

    }

}