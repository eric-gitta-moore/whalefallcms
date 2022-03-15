<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


use app\common\model\user\Reward;

class RewardService
{

    public static function getBookRewards($book_id,$type='cartoon')
    {
        return Reward::where([
            'bookid' => $book_id,
            'status' => $type
        ]) -> sum('score');
    }

}