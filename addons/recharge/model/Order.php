<?php

namespace addons\recharge\model;

use app\common\library\Auth;
use app\common\model\User;
use think\Db;
use think\Exception;
use think\Model;

/**
 * 充值订单模型
 */
class Order extends Model
{

    // 表名
    protected $name = 'recharge_order';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    /**
     * 发起订单支付
     * @param float  $money
     * @param string $paytype
     */
    public static function submitOrder($money, $paytype = 'wechat')
    {
        $auth = Auth::instance();
        $user_id = $auth->isLogin() ? $auth->id : 0;
        $order = null;
        $config = get_addon_config('recharge');
        if ($config && $config['ordercreatelimit']) {
            $order = self::where('user_id', $user_id)
                ->where('amount', $money)
                ->where('status', 'created')
                ->order('id', 'desc')
                ->find();
        }
        $request = \think\Request::instance();
        if (!$order) {
            $orderid = date("Ymdhis") . sprintf("%08d", $user_id) . mt_rand(1000, 9999);
            $data = [
                'orderid'   => $orderid,
                'user_id'   => $user_id,
                'amount'    => $money,
                'payamount' => 0,
                'paytype'   => $paytype,
                'ip'        => $request->ip(),
                'useragent' => substr($request->server('HTTP_USER_AGENT'), 0, 255),
                'status'    => 'created'
            ];
            $order = self::create($data);
        }

        $epay = get_addon_info('epay');
        if ($epay && $epay['state']) {
            $notifyurl = $request->root(true) . '/index/recharge/epay/type/notify/paytype/' . $paytype;
            $returnurl = $request->root(true) . '/index/recharge/epay/type/return/paytype/' . $paytype;

            \addons\epay\library\Service::submitOrder($money, $order->orderid, $paytype, "充值{$money}元", $notifyurl, $returnurl);
            exit;
        } else {
            $result = \think\Hook::listen('recharge_order_submit', $order);
            if (!$result) {
                throw new Exception("请先在后台安装并配置微信支付宝整合插件");
            }
        }
    }

    /**
     * 订单结算
     * @param int    $orderid
     * @param string $payamount
     * @param string $memo
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function settle($orderid, $payamount = null, $memo = '')
    {
        $order = self::getByOrderid($orderid);
        if (!$order) {
            return false;
        }
//        Db::startTrans();
//        try {
            if ($order['status'] != 'paid') {
                $order->payamount = $payamount ? $payamount : $order->amount;
                $order->paytime = time();
                $order->status = 'paid';
                $order->memo = $memo;
                $order->save();

                // 最新版本可直接使用User::money($order->user_id, $order->amount, '充值');来更新
                // 更新会员余额
                $user = User::get($order->user_id);
                if ($user) {
                    $before = $user->money;
                    $after = $user->money + $order->amount;
                    //更新会员信息
                    $user->save(['money' => $after]);
                    //写入日志
                    MoneyLog::create(['user_id' => $order->user_id, 'money' => $order->amount, 'before' => $before, 'after' => $after, 'memo' => '充值']);
                }

                $result = \think\Hook::listen('recharge_order_settled', $order);
            }

//            Db::commit();

            return true;
//        }
//        catch (Exception $exception)
//        {
//            Db::rollback();
//            return false;
//        }
    }
}
