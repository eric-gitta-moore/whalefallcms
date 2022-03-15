<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\user\controller;

use addons\qnbuygroup\model\Buygrouporder;
use addons\recharge\model\Order as RechargeOrder;
use app\common\controller\Userend;
use app\common\model\User;
use think\Db;
use think\View;


/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Userend
{

    /**
     * 查看
     */
    public function index()
    {
        //获取用户等级信息
        $this->auth->getlevel();
//        $this->userStatistics();
//        $this->rechargeStatistics();

        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++) {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $this->view->assign([
//            'totaluser' => 35200,
//            'totalviews' => 219390,
//            'totalorder' => 32143,
//            'totalorderamount' => 174800,
//            'todayuserlogin' => 321,
//            'todayusersignup' => 430,
//            'todayorder' => 2324,
//            'unsettleorder' => 132,
//            'sevendnu' => '80%',
//            'sevendau' => '32%',
            'paylist' => $paylist,
            'createlist' => $createlist,
//            'uploadmode' => $uploadmode
        ]);

        return $this->view->fetch();
    }

    private function userStatistics()
    {
        $user = $this->auth->getUser();
        $agent_path = $user->agent_path . $user -> id . ',';

        //今日新增
        $all_join_today = User::where(['agent_path' => ['like', $agent_path . '%']])->whereTime('createtime', 'today')->count();
        $user_join_today = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '0'])->whereTime('createtime', 'today')->count();
        $agent_join_today = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '1'])->whereTime('createtime', 'today')->count();

        //昨日新增
        $all_join_yesterday = User::where(['agent_path' => ['like', $agent_path . '%']])->whereTime('createtime', 'yesterday')->count();
        $user_join_yesterday = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '0'])->whereTime('createtime', 'yesterday')->count();
        $agent_join_yesterday = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '1'])->whereTime('createtime', 'yesterday')->count();

        //本月新增
        $all_join_this_month = User::where(['agent_path' => ['like', $agent_path . '%']])->whereTime('createtime', 'month')->count();
        $user_join_this_month = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '0'])->whereTime('createtime', 'month')->count();
        $agent_join_this_month = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '1'])->whereTime('createtime', 'month')->count();

        //累计新增
        $all_join_all = User::where(['agent_path' => ['like', $agent_path . '%']])->count();
        $user_join_all = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '0'])->count();
        $agent_join_all = User::where(['agent_path' => ['like', $agent_path . '%'], 'agent_switch' => '1'])->count();

        View::share([
            'all_join_today' => $all_join_today,
            'user_join_today' => $user_join_today,
            'agent_join_today' => $agent_join_today,

            'all_join_yesterday' => $all_join_yesterday,
            'user_join_yesterday' => $user_join_yesterday,
            'agent_join_yesterday' => $agent_join_yesterday,

            'all_join_this_month' => $all_join_this_month,
            'user_join_this_month' => $user_join_this_month,
            'agent_join_this_month' => $agent_join_this_month,

            'all_join_all' => $all_join_all,
            'user_join_all' => $user_join_all,
            'agent_join_all' => $agent_join_all,

        ]);
    }

    private function rechargeStatistics()
    {
        $user = $this->auth->getUser();
        $agent_path = $user->agent_path;
//        $table_prefix = config('database.prefix');
//        $recharge_table = 'recharge_order';
//        $group_table = 'qnbuygroup_order';

        $sub_sql = $user->where(['agent_path' => ['like', $agent_path . $user->id . ',%']])->buildSql();

        $for_arr = [
            'today',
            'yesterday',
            'month',
            'all'
        ];
        $for_arr2 = [
            'recharge' => [
                [
                    //[时间]书币总充值笔数
                    'model' => '\addons\recharge\model\Order',
                    'alias_middle' => '_all_count_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'count',
                    '_operate_param_' => '*',
                ],
                [
                    //[时间]书币总未充值笔数
                    'model' => '\addons\recharge\model\Order',
                    'alias_middle' => '_all_not_paid_count_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => ['<>', 'paid'],
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'count',
                    '_operate_param_' => '*',
                ],
                [
                    //[时间]书币总充值金额
                    'model' => '\addons\recharge\model\Order',
                    'alias_middle' => '_all_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
                [
                    //[时间]书币支付宝总充值金额
                    'model' => '\addons\recharge\model\Order',
                    'alias_middle' => '_zfb_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                        'r.paytype' => 'alipay',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
                [
                    //[时间]书币微信总充值金额
                    'model' => '\addons\recharge\model\Order',
                    'alias_middle' => '_wx_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                        'r.paytype' => 'wechat',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
            ],
            'group' => [
                [
                    //[时间]书币总充值笔数
                    'model' => '\addons\qnbuygroup\model\Buygrouporder',
                    'alias_middle' => '_all_count_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'count',
                    '_operate_param_' => '*',
                ],
                [
                    //[时间]书币总未充值笔数
                    'model' => '\addons\qnbuygroup\model\Buygrouporder',
                    'alias_middle' => '_all_not_paid_count_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => ['<>', 'paid'],
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'count',
                    '_operate_param_' => '*',
                ],
                [
                    //[时间]书币总充值金额
                    'model' => '\addons\qnbuygroup\model\Buygrouporder',
                    'alias_middle' => '_all_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
                [
                    //[时间]书币支付宝总充值金额
                    'model' => '\addons\qnbuygroup\model\Buygrouporder',
                    'alias_middle' => '_zfb_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                        'r.paytype' => 'alipay',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
                [
                    //[时间]书币微信总充值金额
                    'model' => '\addons\qnbuygroup\model\Buygrouporder',
                    'alias_middle' => '_wx_',
                    'where' => [
                        'r.hidden' => '0',
                        'r.status' => 'paid',
                        'r.paytype' => 'wechat',
                    ],
                    '_time_' => 'r.createtime',
                    '_operate_' => 'sum',
                    '_operate_param_' => 'amount',
                ],
            ],
        ];

        $result_arr = [];

        foreach ($for_arr as $time_english) {
            foreach ($for_arr2 as $alias => $models_arr) {
                foreach ($models_arr as $model_arr) {
                    $var_name = $alias . $model_arr['alias_middle'] . $time_english;
                    $var = $model_arr['model']::alias('r')
                        ->field('r.user_id,r.amount,r.hidden,r.createtime')
                        ->join([$sub_sql => 'u'], 'r.user_id = u.id')
                        ->where($model_arr['where']);
                    if (isset($model_arr['_time_']) && $time_english != 'all')
                        $var->whereTime($model_arr['_time_'], $time_english);
                    $result_arr[$var_name] = call_user_func([$var, $model_arr['_operate_']], $model_arr['_operate_param_']);
                }

            }
        }

        $arr = [
//            'recharge_all_count_today' => $result_arr['recharge_all_count_today'],//今日书币充值笔数
//            'group_all_count_today' => $result_arr['group_all_count_today'],//今日用户组充值笔数
            'all_count_today' => $result_arr['recharge_all_count_today'] + $result_arr['group_all_count_today'],//今日总充值笔数
//            'recharge_all_not_paid_count_today' => $result_arr['recharge_all_not_paid_count_today'],//今日书币未充值笔数
//            'group_all_not_paid_count_today' => $result_arr['group_all_not_paid_count_today'],//今日用户组未充值笔数
            'all_not_paid_count_today' => $result_arr['recharge_all_not_paid_count_today'] + $result_arr['group_all_not_paid_count_today'],//今日总未充值笔数
            'all_today' => $result_arr['recharge_all_today'] + $result_arr['group_all_today'],//今日总充值金额
            'zfb_today' => $result_arr['recharge_zfb_today'] + $result_arr['group_zfb_today'],//今日支付宝充值金额
            'wx_today' => $result_arr['recharge_wx_today'] + $result_arr['group_wx_today'],//今日微信充值金额

//            'recharge_all_count_yesterday' => $result_arr['recharge_all_count_yesterday'],//昨日书币充值笔数
//            'group_all_count_yesterday' => $result_arr['group_all_count_yesterday'],//昨日用户组充值笔数
            'all_count_yesterday' => $result_arr['recharge_all_count_yesterday'] + $result_arr['group_all_count_yesterday'],//昨日总充值笔数
//            'recharge_all_not_paid_count_yesterday' => $result_arr['recharge_all_not_paid_count_yesterday'],//昨日书币未充值笔数
//            'group_all_not_paid_count_yesterday' => $result_arr['group_all_not_paid_count_yesterday'],//昨日用户组未充值笔数
            'all_not_paid_count_yesterday' => $result_arr['recharge_all_not_paid_count_yesterday'] + $result_arr['group_all_not_paid_count_yesterday'],//昨日总未充值笔数
            'all_yesterday' => $result_arr['recharge_all_yesterday'] + $result_arr['group_all_yesterday'],//昨日总充值金额
            'zfb_yesterday' => $result_arr['recharge_zfb_yesterday'] + $result_arr['group_zfb_yesterday'],//昨日支付宝充值金额
            'wx_yesterday' => $result_arr['recharge_wx_yesterday'] + $result_arr['group_wx_yesterday'],//昨日微信充值金额

//            'recharge_all_count_month' => $result_arr['recharge_all_count_month'],//本月书币充值笔数
//            'group_all_count_month' => $result_arr['group_all_count_month'],//本月用户组充值笔数
            'all_count_month' => $result_arr['recharge_all_count_month'] + $result_arr['group_all_count_month'],//本月总充值笔数
//            'recharge_all_not_paid_count_month' => $result_arr['recharge_all_not_paid_count_month'],//本月书币未充值笔数
//            'group_all_not_paid_count_month' => $result_arr['group_all_not_paid_count_month'],//本月用户组未充值笔数
            'all_not_paid_count_month' => $result_arr['recharge_all_not_paid_count_month'] + $result_arr['group_all_not_paid_count_month'],//本月总未充值笔数
            'all_month' => $result_arr['recharge_all_month'] + $result_arr['group_all_month'],//本月总充值金额
            'zfb_month' => $result_arr['recharge_zfb_month'] + $result_arr['group_zfb_month'],//本月支付宝充值金额
            'wx_month' => $result_arr['recharge_wx_month'] + $result_arr['group_wx_month'],//本月微信充值金额

//            'recharge_all_count_all' => $result_arr['recharge_all_count_all'],//累积书币充值笔数
//            'group_all_count_all' => $result_arr['group_all_count_all'],//累积用户组充值笔数
            'all_count_all' => $result_arr['recharge_all_count_all'] + $result_arr['group_all_count_all'],//累积总充值笔数
//            'recharge_all_not_paid_count_all' => $result_arr['recharge_all_not_paid_count_all'],//累积书币未充值笔数
//            'group_all_not_paid_count_all' => $result_arr['group_all_not_paid_count_all'],//累积用户组未充值笔数
            'all_not_paid_count_all' => $result_arr['recharge_all_not_paid_count_all'] + $result_arr['group_all_not_paid_count_all'],//累积总未充值笔数
            'all_all' => $result_arr['recharge_all_all'] + $result_arr['group_all_all'],//累积总充值金额
            'zfb_all' => $result_arr['recharge_zfb_all'] + $result_arr['group_zfb_all'],//累积支付宝充值金额
            'wx_all' => $result_arr['recharge_wx_all'] + $result_arr['group_wx_all'],//累积微信充值金额
        ];

        $arr = array_merge($arr,$result_arr);

        $arr['today_percent'] = $arr['all_count_today']/(($arr['all_count_today']+$arr['all_not_paid_count_today'])!=0?:1)*100;//今日总充值成功率
        $arr['today_score_percent'] = $arr['recharge_all_count_today']/(($arr['recharge_all_count_today']+$arr['recharge_all_not_paid_count_today'])!=0?:1)*100;//今日书币充值成功率
        $arr['today_group_percent'] = $arr['group_all_count_today']/(($arr['group_all_count_today']+$arr['group_all_not_paid_count_today'])!=0?:1)*100;//今日用户组充值成功率

        $arr['yesterday_percent'] = $arr['all_count_yesterday']/(($arr['all_count_yesterday']+$arr['all_not_paid_count_yesterday'])!=0?:1)*100;//昨日总充值成功率
        $arr['yesterday_score_percent'] = $arr['recharge_all_count_yesterday']/(($arr['recharge_all_count_yesterday']+$arr['recharge_all_not_paid_count_yesterday'])!=0?:1)*100;//昨日书币充值成功率
        $arr['yesterday_group_percent'] = $arr['group_all_count_yesterday']/(($arr['group_all_count_yesterday']+$arr['group_all_not_paid_count_yesterday'])!=0?:1)*100;//昨日书币充值成功率

        $arr['month_percent'] = $arr['all_count_month']/(($arr['all_count_month']+$arr['all_not_paid_count_month'])!=0?:1)*100;//本月总充值成功率
        $arr['month_score_percent'] = $arr['recharge_all_count_month']/(($arr['recharge_all_count_month']+$arr['recharge_all_not_paid_count_month'])!=0?:1)*100;//本月书币充值成功率
        $arr['month_group_percent'] = $arr['group_all_count_month']/(($arr['group_all_count_month']+$arr['group_all_not_paid_count_month'])!=0?:1)*100;//本月用户组充值成功率

        $arr['all_percent'] = $arr['all_count_all']/(($arr['all_count_all']+$arr['all_not_paid_count_all'])!=0?:1)*100;//累积总充值成功率
        $arr['all_score_percent'] = $arr['recharge_all_count_all']/(($arr['recharge_all_count_all']+$arr['recharge_all_not_paid_count_all'])!=0?:1)*100;//累积书币充值成功率
        $arr['all_group_percent'] = $arr['group_all_count_all']/(($arr['group_all_count_all']+$arr['group_all_not_paid_count_all'])!=0?:1)*100;//累积用户组充值成功率


        foreach ($arr as $key => &$value) {
            if (stripos($key,'count') === false && stripos($key,'percent') === false)
            {
                $value = number_format($value,2);
            }
            else
            {
                $value = intval($value);
            }
        }
//        halt($arr);

        //渲染变量
        View::share($arr);
    }

}
