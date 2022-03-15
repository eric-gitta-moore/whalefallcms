<?php


namespace addons\agent\library;


use app\common\model\agent\Distribution;

class Service
{
    /**
     * 解析代理路径
     * @param string $path
     * @return array
     */
    public static function parse($path)
    {
        return explode(',',rtrim($path,','));
    }

    /**
     * 获取可获得分润的代理路径
     * @param array $path
     * @return array
     * @throws \think\Exception
     */
    public static function getActivePath(array $path)
    {
        //$path = [0,1,2,3,4,5,6,7,8,9]
        //$path = [0,1,2]
        //$path = [0,1]
        //$path = [0]

        $distribution_num = Distribution::count();//4

        if (empty($distribution_num) || $distribution_num == 0)
        {//零级分销
            return [];
        }
        else
        {
            unset($path[0]);//$path = [1,2]
            $path = array_reverse($path);//$path = [2,1]
            $path = array_slice($path,0,$distribution_num);
            return  array_reverse($path);
        }
    }

    public static function getDistributionMap()
    {
        $data = Distribution::order('rank') -> field('rank,proportion') -> select();
        $distribution_map = [];

        foreach ($data as $datum) {
            $distribution_map[$datum -> rank] = $datum -> proportion;
        }

        return $distribution_map;
    }
}