<?php

namespace addons\qnbuygroup\model;

use think\Exception;
use think\Model;
use app\common\library\Auth;
use app\common\model\User;
use addons\qnbuygroup\model\Buygroupuser;
use app\admin\model\qnbuygroup\Buygroupset;
use think\Log;

/**
 * 购买用户组订单
 * Class Buygrouporder
 * @package addons\qnbuygroup\model
 */
class Buygrouporder extends Model
{

    // 表名
    protected $name = 'qnbuygroup_order';
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
     * @param float $money
     * @param string $paytype
     */
    public static function submitOrder($buygroupset, $paytype = 'wechat')
    {
        $auth = Auth::instance();
        $money = $buygroupset['amount'];
        $user_id = $auth->isLogin() ? $auth->id : 0;
        $order = null;
        $request = \think\Request::instance();
        if (!$order) {
            $orderid = date("Ymdhis") . sprintf("%08d", $user_id) . mt_rand(1000, 9999);
            $data = [
                'orderid' => $orderid,
                'user_id' => $user_id,
                'amount' => $money,
                'group_id' => $buygroupset['id'],
                'payamount' => 0,
                'paytype' => $paytype,
                'ip' => $request->ip(),
                'useragent' => substr($request->server('HTTP_USER_AGENT'), 0, 255),
                'status' => 'created'
            ];
            $order = self::create($data);
        }

        $epay = get_addon_info('epay');
        if ($epay && $epay['state']) {
            $notifyurl = $request->root(true) . '/index/qnbuygroup.buygroup/epay/type/notify/paytype/' . $paytype;
            $returnurl = $request->root(true) . '/index/qnbuygroup.buygroup/epay/type/return/paytype/' . $paytype;

            \addons\epay\library\Service::submitOrder(number_format($money, 2, '.', ''), $order->orderid, $paytype, "购买{$buygroupset['groupname']}", $notifyurl, $returnurl);
            exit;
        } else {
            $result = \think\Hook::listen('qnbuygroup_order_submit', $order);
            if (!$result) {
                throw new Exception("请先在后台安装并配置微信支付宝整合插件");
            }
        }
    }

    /**
     * 订单支付结果
     * @param $orderid
     * @param null $payamount
     * @param string $memo
     * @return bool
     */
    public static function settle($orderid, $payamount = null, $memo = '')
    {
        $order = self::getByOrderid($orderid);
        if (!$order) {
            return false;
        }
        if ($order['status'] != 'paid') {
            $order->payamount = $payamount ? $payamount : $order->amount;
            $order->paytime = time();
            $order->status = 'paid';
            $order->memo = $memo;
            $order->save();
            $groupid = $order['group_id'];
            $groupset = Buygroupset::where("id", $groupid)->find();

            if ($groupset) {
                $data = [
                    'user_id' => $order['user_id'],
                    'group_id' => $groupset['id'],
                    'orderid' => $orderid,
                    'expiredtime' => strtotime(date("Y-m-d", strtotime('+' . $groupset['exp'] . ' day', time()))),
                ];
                Buygroupuser::create($data);

                $user = User::get($order->user_id);

                if ($user) {
                    $user->save(['group_id' => $groupset['group_id']]);
                }
            }
            $result = \think\Hook::listen('qnbuggroup_order_settled', $order);
        }
        return true;
    }

}