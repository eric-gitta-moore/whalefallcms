<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller;

use addons\recharge\model\Order;
use addons\qnbuygroup\model\Buygrouporder;
use app\common\controller\Backend;
use app\common\model\cartoon\Cartoon;
use app\common\model\novel\Novel;
use app\common\model\User;
use app\common\model\user\Buylog;
use think\Config;
use think\Db;
use think\View;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{
    protected $stat = [];

    /**
     * 查看
     */
    public function index()
    {
        $this->initStatistics();
        $this->initCharts();
        $this->initPayBookRank('cartoon');
        $this->initPayBookRank('novel');
        $this -> calculationDatabase();

        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++) {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
        }

        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([
//            'totaluser'        => 35200,
//            'totalviews'       => 219390,
//            'totalorder'       => 32143,
//            'totalorderamount' => 174800,
//            'todayuserlogin'   => 321,
//            'todayusersignup'  => 430,
//            'todayorder'       => 2324,
//            'unsettleorder'    => 132,
//            'sevendnu'         => '80%',
//            'sevendau'         => '32%',
//            'paylist'          => $paylist,
//            'createlist'       => $createlist,
            'addonversion' => $addonVersion,
            'uploadmode' => $uploadmode
        ]);

        return $this->view->fetch();
    }

    protected function calculationDatabase() {

        $dbsize = 0;
        $info = Db::query('SHOW TABLE STATUS LIKE "' . \config('database.prefix') . '%"');

        foreach ($info as $table) {
            $dbsize += $table['Data_length'] + $table['Index_length'];
        }

        $units = array(' B', ' KB', ' MB', ' GB', ' TB');

        for ($i = 0; $dbsize > 1024; $i++) { $dbsize /= 1024; }

        $return = round($dbsize, 2).$units[$i];
        View::share([
            'database_size' => $return
        ]);
    }

    /**
     * 初始化书本购买排行榜
     * @param bool|string $type
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function initPayBookRank($type = false, $limit = 10)
    {
        $type = check_type($type);
//        halt($type);
        Db::execute('set sql_mode =\'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION\';');
        if (empty($type)) {
            $rank = Buylog::field('status,count(*) as cnt,bookid')->limit($limit)->group('bookid')->order('cnt desc')->fetchSql(false)->select();
        } else {
            $rank = Buylog::field('status,count(*) as cnt,bookid')->where('status', $type)->limit($limit)->group('bookid')->order('cnt desc')->fetchSql(false)->select();
        }


        foreach ($rank as $item) {
            load_relation($rank, $item['status']);
        }

        if (count($rank) < $limit) {
            for ($i = count($rank); $i < $limit; $i++) {
                $rank[$i] = '';
            }
        }

        trace($rank);

        if (empty($type)) {
            View::share([
                'pay_book_rank' => $rank
            ]);
        } else {
            View::share([
                $type . '_pay_book_rank' => $rank
            ]);
        }
    }

    /**
     * 初始化表格参数
     */
    protected function initCharts()
    {
        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++) {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = 0;
            $paylist[$day] = 0;
        }


        $prefix = \config('database.prefix');
        $recharge_all_orders = Db::query('SELECT cast(FROM_UNIXTIME(`createtime`) as date) as \'date\',COUNT(0) as \'cnt\'  FROM `' . $prefix . 'recharge_order` WHERE `createtime` >= ' . $seventtime . ' GROUP BY cast(FROM_UNIXTIME(`createtime`) as date)');
        $recharge_paid_orders = Db::query('SELECT cast(FROM_UNIXTIME(`createtime`) as date) as \'date\',COUNT(0) as \'cnt\'  FROM `' . $prefix . 'recharge_order` WHERE `status`=\'paid\' AND `createtime` >= ' . $seventtime . ' GROUP BY cast(FROM_UNIXTIME(`createtime`) as date)');
        $group_all_orders = Db::query('SELECT cast(FROM_UNIXTIME(`createtime`) as date) as \'date\',COUNT(0) as \'cnt\'  FROM `' . $prefix . 'qnbuygroup_order` WHERE `createtime` >= ' . $seventtime . ' GROUP BY cast(FROM_UNIXTIME(`createtime`) as date)');
        $group_paid_orders = Db::query('SELECT cast(FROM_UNIXTIME(`createtime`) as date) as \'date\',COUNT(0) as \'cnt\'  FROM `' . $prefix . 'qnbuygroup_order` WHERE `status`=\'paid\' AND `createtime` >= ' . $seventtime . ' GROUP BY cast(FROM_UNIXTIME(`createtime`) as date)');
        $recharge_all_orders = self::catchInnerKeyToMainKey($recharge_all_orders, 'date', 'cnt');
        $recharge_paid_orders = self::catchInnerKeyToMainKey($recharge_paid_orders, 'date', 'cnt');
        $group_all_orders = self::catchInnerKeyToMainKey($group_all_orders, 'date', 'cnt');
        $group_paid_orders = self::catchInnerKeyToMainKey($group_paid_orders, 'date', 'cnt');
        $all_orders = self::add_arr($recharge_all_orders, $group_all_orders);
        $paid_orders = self::add_arr($recharge_paid_orders, $group_paid_orders);
        $all_orders = self::add_arr($all_orders, $createlist);
        $paid_orders = self::add_arr($paid_orders, $paylist);

//        trace($all_orders);
//        trace($paid_orders);
//        halt(1);
        View::share([
            'paylist' => $paid_orders,
            'createlist' => $all_orders,
        ]);
    }

    /**
     * 初始化统计参数
     * @throws \think\Exception
     */
    protected function initStatistics()
    {
        $stat = [];

        //总漫画数
        $stat['total_cartoons_num'] = Cartoon::count();
        //总小说数
        $stat['total_novels_num'] = Novel::count();

        //总会员数
        $stat['total_users_num'] = User::count();
        //今日注册数
        $stat['user_register_today_count'] = User::whereTime('jointime', 'today')->count();
        //今日登录数
        $stat['user_login_today_count'] = User::whereTime('logintime', 'today')->count();
        //昨日注册数
        $stat['user_register_yesterday_count'] = User::whereTime('jointime', 'yesterday')->count();
        //昨日登录数
        $stat['user_login_yesterday_count'] = User::whereTime('logintime', 'yesterday')->count();

        //+----------------------------------------------------------------------------------
        //||
        //||    用户组
        //||
        //+----------------------------------------------------------------------------------

        //用户组总下单数
        $stat['group_all_orders_num'] = Buygrouporder::count();
        //用户组成功付款数
        $stat['group_paid_orders_num'] = Buygrouporder::where('status', 'paid')->count();
        //用户组购买总金额
        $stat['group_all_orders_money'] = Buygrouporder::sum('amount');
        //用户组成功付款金额
        $stat['group_paid_orders_money'] = Buygrouporder::where('status', 'paid')->sum('amount');
        //用户组扣量单数
        $stat['group_hidden_orders_num'] = Buygrouporder::where(['status' => 'paid', 'hidden' => '1'])->count();
        //用户组扣量金额
        $stat['group_hidden_orders_money'] = Buygrouporder::where(['status' => 'paid', 'hidden' => '1'])->sum('amount');

        //昨日用户组下单数
        $stat['group_yesterday_all_orders_num'] = Buygrouporder::whereTime('createtime', 'yesterday')->count();
        //昨日用户组成功付款数
        $stat['group_yesterday_paid_orders_num'] = Buygrouporder::whereTime('createtime', 'yesterday')->where('status', 'paid')->count();
        //昨日用户组下单金额
        $stat['group_yesterday_all_orders_money'] = Buygrouporder::whereTime('createtime', 'yesterday')->sum('amount');
        //昨日用户组成功付款金额
        $stat['group_yesterday_paid_orders_money'] = Buygrouporder::whereTime('createtime', 'yesterday')->where('status', 'paid')->sum('amount');

        //今日用户组下单数
        $stat['group_today_all_orders_num'] = Buygrouporder::whereTime('createtime', 'today')->count();
        //今日用户组成功付款数
        $stat['group_today_paid_orders_num'] = Buygrouporder::whereTime('createtime', 'today')->where('status', 'paid')->count();
        //今日用户组下单金额
        $stat['group_today_all_orders_money'] = Buygrouporder::whereTime('createtime', 'today')->sum('amount');
        //今日用户组成功付款金额
        $stat['group_today_paid_orders_money'] = Buygrouporder::whereTime('createtime', 'today')->where('status', 'paid')->sum('amount');

        //本周用户组下单数
        $stat['group_week_all_orders_num'] = Buygrouporder::whereTime('createtime', 'week')->count();
        //本周用户组成功付款数
        $stat['group_week_paid_orders_num'] = Buygrouporder::whereTime('createtime', 'week')->where('status', 'paid')->count();
        //本周用户组下单金额
        $stat['group_week_all_orders_money'] = Buygrouporder::whereTime('createtime', 'week')->sum('amount');
        //本周用户组成功付款金额
        $stat['group_week_paid_orders_money'] = Buygrouporder::whereTime('createtime', 'week')->where('status', 'paid')->sum('amount');

        //本月用户组下单数
        $stat['group_month_all_orders_num'] = Buygrouporder::whereTime('createtime', 'month')->count();
        //本月用户组成功付款数
        $stat['group_month_paid_orders_num'] = Buygrouporder::whereTime('createtime', 'month')->where('status', 'paid')->count();
        //本月用户组下单金额
        $stat['group_month_all_orders_money'] = Buygrouporder::whereTime('createtime', 'month')->sum('amount');
        //本月用户组成功付款金额
        $stat['group_month_paid_orders_money'] = Buygrouporder::whereTime('createtime', 'month')->where('status', 'paid')->sum('amount');
        //本月用户组总订单数
        $stat['group_month_all_orders_num'] = Buygrouporder::whereTime('createtime', 'month')->count();
        //本月用户组成功订单数
        $stat['group_month_paid_orders_num'] = Buygrouporder::whereTime('createtime', 'month')->where('status', 'paid')->count();


        //+----------------------------------------------------------------------------------
        //||
        //||    充值
        //||
        //+----------------------------------------------------------------------------------
        //总充值单数
        $stat['charge_all_orders_num'] = Order::count();
        //总充值金额
        $stat['charge_all_orders_money'] = Order::sum('amount');
        //充值成功付款单数
        $stat['charge_paid_orders_num'] = Order::where('status', 'paid')->count();
        //充值成功付款金额
        $stat['charge_paid_orders_money'] = Order::where('status', 'paid')->sum('amount');
        //充值扣量总单数
        $stat['charge_hidden_orders_num'] = Order::where(['status' => 'paid', 'hidden' => '1'])->count();
        //充值扣量总金额
        $stat['charge_hidden_orders_money'] = Order::where(['status' => 'paid', 'hidden' => '1'])->sum('amount');

        //昨日充值单数
        $stat['charge_yesterday_all_orders_num'] = Order::whereTime('createtime', 'yesterday')->count();
        //昨日充值金额
        $stat['charge_yesterday_all_orders_money'] = Order::whereTime('createtime', 'yesterday')->sum('amount');
        //昨日充值扣量单数
        $stat['charge_yesterday_hidden_orders_num'] = Order::whereTime('createtime', 'yesterday')->where(['status' => 'paid', 'hidden' => '1'])->count();
        //昨日充值扣量金额
        $stat['charge_yesterday_hidden_orders_money'] = Order::whereTime('createtime', 'yesterday')->where(['status' => 'paid', 'hidden' => '1'])->sum('amount');
        //昨日充值成功付款单数
        $stat['charge_yesterday_paid_orders_num'] = Order::whereTime('createtime', 'yesterday')->where('status', 'paid')->count();
        //昨日充值成功付款金额
        $stat['charge_yesterday_paid_orders_money'] = Order::whereTime('createtime', 'yesterday')->where('status', 'paid')->sum('amount');

        //今日充值单数
        $stat['charge_today_all_orders_num'] = Order::whereTime('createtime', 'today')->count();
        //今日充值金额
        $stat['charge_today_all_orders_money'] = Order::whereTime('createtime', 'today')->sum('amount');
        //今日充值成功付款单数
        $stat['charge_today_paid_orders_num'] = Order::whereTime('createtime', 'today')->where('status', 'paid')->count();
        //今日充值成功付款金额
        $stat['charge_today_paid_orders_money'] = Order::whereTime('createtime', 'today')->where('status', 'paid')->sum('amount');
        //今日充值扣量单数
        $stat['charge_today_hidden_orders_num'] = Order::whereTime('createtime', 'today')->where(['status' => 'paid', 'hidden' => '1'])->count();
        //今日充值扣量金额
        $stat['charge_today_hidden_orders_money'] = Order::whereTime('createtime', 'today')->where(['status' => 'paid', 'hidden' => '1'])->sum('amount');

        //本周充值下单数
        $stat['charge_week_all_orders_num'] = Order::whereTime('createtime', 'week')->count();
        //本周充值成功付款数
        $stat['charge_week_paid_orders_num'] = Order::whereTime('createtime', 'week')->where('status', 'paid')->count();
        //本周充值下单金额
        $stat['charge_week_all_orders_money'] = Order::whereTime('createtime', 'week')->sum('amount');
        //本周充值成功付款金额
        $stat['charge_week_paid_orders_money'] = Order::whereTime('createtime', 'week')->where('status', 'paid')->sum('amount');

        //本月充值总订单数
        $stat['charge_month_all_orders_num'] = Order::whereTime('createtime', 'month')->count();
        //本月充值总金额
        $stat['charge_month_all_orders_money'] = Order::whereTime('createtime', 'month')->where('status', 'paid')->sum('amount');
        //本月充值成功付款单数
        $stat['charge_month_paid_orders_num'] = Order::whereTime('createtime', 'month')->where('status', 'paid')->count();
        //本月充值成功付款金额
        $stat['charge_month_paid_orders_money'] = Order::whereTime('createtime', 'month')->where('status', 'paid')->sum('amount');


        //+----------------------------------------------------------------------------------
        //||
        //||    代理和VIP
        //||
        //+----------------------------------------------------------------------------------
        //代理总数
        $stat['agent_count'] = User::where(['agent_switch' => 1])->count();
        $stat['cartoon_vip_group_count'] = User::where(['group_id' => \config('site.group_cartoon_vip_id')])->count();
        $stat['novel_vip_group_count'] = User::where(['group_id' => \config('site.group_novel_vip_id')])->count();
        $stat['all_vip_group_count'] = User::where(['group_id' => \config('site.group_all_vip_id')])->count();
        $stat['vip_group_percent'] = $stat['group_month_paid_orders_num'] / ($stat['group_month_all_orders_num'] ?: 1);

        $this->stat = $stat;
        $this->countStatistics();


        foreach ($this->stat as $key => &$item) {
            if (stripos($key, 'money') !== false || stripos($key, 'percent') !== false) {
                $item = number_format($item, 2);
            }
        }
//        $total_books = $total_cartoons_num . '+' . $total_novels_num;

        View::share($this->stat);
    }

    /**
     * 计算统计参数
     */
    protected function countStatistics()
    {
        $stat = &$this->stat;
        //总书本数量
        $stat['total_books_num'] = $stat['total_cartoons_num'] + $stat['total_novels_num'];
        //总扣量单数
        $stat['total_hidden_orders_num'] = $stat['group_hidden_orders_num'] + $stat['charge_hidden_orders_num'];
        //总扣量金额
        $stat['total_hidden_orders_money'] = $stat['group_hidden_orders_money'] + $stat['charge_hidden_orders_money'];

        //总收益
        $stat['total_paid_orders_money'] = $stat['group_paid_orders_money'] + $stat['charge_paid_orders_money'];
        //总交易额,包括未付
        $stat['total_all_orders_money'] = $stat['group_all_orders_money'] + $stat['charge_all_orders_money'];
        //总交易单数
        $stat['total_all_orders_num'] = $stat['group_all_orders_num'] + $stat['charge_all_orders_num'];

        //昨日订单数
        $stat['yesterday_all_orders_num'] = $stat['group_yesterday_all_orders_num'] + $stat['charge_yesterday_all_orders_num'];
        //昨日订单金额
        $stat['yesterday_all_orders_money'] = $stat['group_yesterday_all_orders_money'] + $stat['charge_yesterday_all_orders_money'];
        //昨日成功订单数
        $stat['yesterday_paid_orders_num'] = $stat['group_yesterday_paid_orders_num'] + $stat['charge_yesterday_paid_orders_num'];
        //昨日成功订单金额
        $stat['yesterday_paid_orders_money'] = $stat['group_yesterday_paid_orders_money'] + $stat['charge_yesterday_paid_orders_money'];
        //昨日未付订单数
        $stat['yesterday_not_paid_orders_num'] = $stat['yesterday_all_orders_num'] - $stat['yesterday_paid_orders_num'];
        //昨日未付订单金额
        $stat['yesterday_not_paid_orders_money'] = $stat['yesterday_all_orders_money'] - $stat['yesterday_paid_orders_money'];

        //今日订单数
        $stat['today_all_orders_num'] = $stat['group_today_all_orders_num'] + $stat['charge_today_all_orders_num'];
        //今日订单金额
        $stat['today_all_orders_money'] = $stat['group_today_all_orders_money'] + $stat['charge_today_all_orders_money'];
        //今日成功订单金额
        $stat['today_paid_orders_money'] = $stat['group_today_paid_orders_money'] + $stat['charge_today_paid_orders_money'];
        //今日成功订单
        $stat['today_paid_orders_num'] = $stat['group_today_paid_orders_num'] + $stat['charge_today_paid_orders_num'];
        //今日未付订单数
        $stat['today_not_paid_orders_num'] = $stat['today_all_orders_num'] - $stat['today_paid_orders_num'];
        //今日未付订单金额
        $stat['today_not_paid_orders_money'] = $stat['today_all_orders_money'] - $stat['today_paid_orders_money'];
        //今日成功率
        $stat['today_paid_percent'] = $stat['today_paid_orders_num'] / ($stat['today_all_orders_num'] ?: 1) * 100;


        //+----------------------------------------------------------------------------------
        //||
        //||    下排大表格统计
        //||
        //+----------------------------------------------------------------------------------

        //本周订单数
        $stat['week_all_orders_num'] = $stat['group_week_all_orders_num'] + $stat['charge_week_all_orders_num'];
        //本周成功订单数
        $stat['week_paid_orders_num'] = $stat['group_week_paid_orders_num'] + $stat['charge_week_paid_orders_num'];
        //本周成功订单金额
        $stat['week_paid_orders_money'] = $stat['group_week_paid_orders_money'] + $stat['charge_week_paid_orders_money'];
        //本周成功率
        $stat['week_paid_percent'] = $stat['week_paid_orders_num'] / ($stat['week_all_orders_num'] ?: 1) * 100;

        //本月订单数
        $stat['month_all_orders_num'] = $stat['group_month_all_orders_num'] + $stat['charge_month_all_orders_num'];
        //本周成功订单金额
        $stat['month_paid_orders_money'] = $stat['group_month_paid_orders_money'] + $stat['charge_month_paid_orders_money'];
        //本月成功订单数
        $stat['month_paid_orders_num'] = $stat['group_month_paid_orders_num'] + $stat['charge_month_paid_orders_num'];
        //本月成功率
        $stat['month_paid_percent'] = $stat['month_paid_orders_num'] / ($stat['month_all_orders_num'] ?: 1) * 100;
    }


    /**
     * 多维数组中，取出指定键名和键值做为新数组的键名和键值
     * @param array $array
     * @param string $inner_key
     * @param string $content_key
     * @return array
     */
    protected static function catchInnerKeyToMainKey($array, $inner_key, $content_key)
    {
        $out = [];
        foreach ($array as $item) {
            $out[$item[$inner_key]] = $item[$content_key];
        }
        return $out;
    }

    /**
     * 把两个数组相同下标的值相加
     * @param array $array_1
     * @param array $array_2
     * @return array
     */
    public static function add_arr($array_1, $array_2)
    {

        if (is_array($array_1) && is_array($array_2)) {

            foreach ($array_1 as $k => $v) {
                if (isset($array_2[$k])) {
                    $array_1[$k] = $v + $array_2[$k];
                }
            }

            return $array_1 + $array_2;
        }

    }
}
