<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;

use \app\common\model\config\Cate as CateModel;

class CateService
{
    public static function getCate($type = null, $name2key = false)
    {
        if (!empty($type)) {
            $data = CateModel::where(['status' => $type])->select();
        } else {
            $data = CateModel::all();
        }

        $out = [];
        if ($name2key) {
            foreach ($data as $v) {
                $out[$v['name']] = $v;
            }
        } else {
            if (!empty($data))
                $out = collection($data)->toArray();
        }

        return $out;

    }

}