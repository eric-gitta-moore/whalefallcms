<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service\cartoon;


use app\common\model\cartoon\Cartoon;

class CartoonService
{

    public static function getBooks($where = '1=1',$order = 'id',$limit = 6,$cache = true)
    {
        $books = Cartoon::with('author') -> where($where) -> order($order) -> limit($limit) -> cache(true,config('site.query_cache')) -> select();

        return $books;
    }


}