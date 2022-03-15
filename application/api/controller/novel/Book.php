<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\api\controller\novel;


class Book extends \app\api\controller\Book
{
    /**
     * 可以为cartoon，novel，listen
     * @var string 类型，小写
     */
    protected $module = 'novel';
}