<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\api\controller;


use app\common\controller\Api;
use app\service\UserGroupService;
use app\service\UserService;

abstract class Chapter extends Api
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [
        'get'
    ];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [
        '*'
    ];

    /**
     * 可以为cartoon，novel，listen
     * @var string 类型，小写
     */
    protected $module = 'cartoon';

    public function get($id)
    {
        $model = '\app\common\model\\' . $this->module;
        $chapter_model = $model . '\Chapter';
        $book_model = $model . '\\' . ucfirst($this->module);
        $book_primary_key = $this->module . '_' . $this->module . '_id';
        $chapter = $chapter_model::with('photos,book') -> where('id',$id) -> find();
        $book = &$chapter['book'];

        if (!is_null($chapter))
            $chapter = $chapter -> toArray();

//        判断是否删减
        $vip_group_set = UserGroupService::getVipGroups();

        //判断该章节是否为付费章节
        $current_chapter_order = $chapter_model::where([
            $book_primary_key => $chapter[$book_primary_key],
            'weigh' => ['<=', $chapter['weigh']]
        ])-> order('weigh')->count();

        $need_pay = false;
        $payed = false;
        $is_show_tips = false;
        $tips_msg = false;
        $append_html = '';
        do{
            //收费类型:0=免费,1=收费
            if ($book['free_type'] == 0)
                break;

            //当前章节数小于起始付费章节数，则跳过
            if ($current_chapter_order < $book['start_pay'])
                break;

            //略过VIP
            //vip_type:VIP全免费:0=否,1=是
            //这里排除至尊VIP
            if ($book['vip_type'] == 0 && !is_null($this->auth))//如果VIp也不是免费的
            {
                $current_vip_group_id = $vip_group_set[$this->module];
                $user = $this->auth->getUser();
//                        //普通VIP
//                        if (in_array($user -> group_id,[$current_vip_group_id] ) )
//                        {
//                            //普通VIP依旧删减
//                        }
                //至尊VIP
                if ($user->group_id == $vip_group_set['all']) {
                    //免费
                    //不删减
                    $is_show_tips = true;
                    $tips_msg = '您是尊贵的VIP会员<br />已经为您自动显示全部内容';
                    break;
                }

            }
            //判断购买情况
            if (!is_null($this->auth))
            {
                $user = $this->auth->getUser();
                $is_show_tips = true;


//                trace($chapter);
                //判断是否购买
                $buylog = UserService::getUserBuyBookStatus($user -> id,$chapter[$book_primary_key],$chapter['id'],$this->module,true);
//                trace($buylog);
                if (!empty($buylog))
                {
                    //已购
                    $tips_msg = '您已购买<br />已经为您自动显示全部内容';
                    $payed = true;
                    break;
                }
                else
                {
                    //未购

                    //判断自动购买
                    $auto_pay = session('auto_pay');
                    if ($auto_pay)
                    {
                        $tips_msg = UserService::buyChapter($chapter['id'],$user,$this->module);
                    }
                    if ($tips_msg === true)
                    {
                        $tips_msg = '已为您自动购买本章节' . '<br />当前' . config('site.score_name') . ':' . strval(intval($user -> score) - intval($chapter['money']));
                        break;
                    }

//                    $payed = true;
                }

            }


            //删减图片
            if ($this->module == 'cartoon')
            {
                $append_html = config('site.cartoon_need_pay_show_html');
                $chapter['photos'] = [$chapter['photos'][0]];
            }
            elseif ($this->module == 'novel')
            {
                $append_html = config('site.novel_need_pay_show_html');
                $chapter['content'] = mb_strcut($chapter['content'],0,config('site.novel_need_pay_cut_length') * 2) . '...';

            }

            $is_show_tips = true;
            $tips_msg = '您当前还未购买本章节哦<br />' . $tips_msg;
            $need_pay = true;
        }while(false);


        $chapter['info']['payed'] = $payed;
        $chapter['info']['need_pay'] = $need_pay;
        $chapter['info']['is_show_tips'] = $is_show_tips;
        $chapter['info']['append_html'] = $append_html;
        $chapter['info']['tips_msg'] = $tips_msg;
        unset($chapter['book']);

        $this->success('获取成功',$chapter);
    }
}