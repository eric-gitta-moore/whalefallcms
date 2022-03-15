<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\Exception;
use think\exception\DbException;

class Reward extends Api
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'get',
//        '*'
    ];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [
//        '*'
    ];

    /**
     * 获取打赏记录
     * @param $page
     * @param $book_id
     * @param string $type
     * @param bool $num
     * @return |null
     * @throws \think\exception\DbException
     */
    public function get($page,$book_id,$type='cartoon', $num = false)
    {
        $type = strtolower($type);
        if (empty($num))
            $num = config('site.api_reward_num');

        if (empty($num) || empty($book_id) || empty($type) || $page <= 0)
            return null;

        $info = \app\common\model\user\Reward::where([
            'bookid' => $book_id,
            'status' => $type
        ]) -> with('user') -> paginate([
            'page' => $page,
            'list_rows' => $num
        ]);


        $gift = config('site.reward_gift');
        $images = config('site.gift_images');
        $gifts = [];
        $i = 0;
        foreach ($gift as $value) {
            $gifts[$value] = $i;
            $i++;
        }

        foreach ($info as &$item) {
            $item['image'] = $images[$gifts[$item['score']]];
        }

        $this->success('success',$info -> toArray());
    }

    public function doReward($score,$book_id,$type='cartoon')
    {
        if (!in_array($score,config('site.reward_gift')))
            $this->error('参数错误');

        $user = $this->auth -> getUser();
        if ($user -> score < $score)
            $this->error(config('site.score_name').'余额不足');

        Db::startTrans();
        try {
            \app\common\model\User::score(0-intval($score),$user -> id,'打赏书本(' . $type . ')id:' . $book_id);
            $r = \app\common\model\user\Reward::insert([
                'bookid' => $book_id,
                'status' => $type,
                'score' => abs($score),
                'createtime' => time(),
                'user_id' => $user -> id
            ]);
            if ($r)
            {
                Db::commit();
                $this->success('打赏成功');
            }
            else
                $this->success('打赏失败');
        }
        catch (DbException $exception)
        {
            Db::rollback();
            $this->error($exception -> getMessage());
        }

    }
}