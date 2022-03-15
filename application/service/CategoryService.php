<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;

use app\common\model\Category as CategoryModel;

class CategoryService
{
    /**
     * 获取系统分类
     * @param null|string $type 类型，cartoon novel listen
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCate($type = null)
    {
        if (!empty($type))
            $origin_cate = CategoryModel::where('type', $type)->select();
        else
            $origin_cate = CategoryModel::all();
//        trace(collection($origin_cate) -> toArray());
        $cate = [];
        foreach ($origin_cate as $item) {
            $cate[$item['nickname']] = $item;
        }
        return $cate;
    }

}