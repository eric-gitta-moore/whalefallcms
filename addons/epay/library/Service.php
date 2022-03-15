<?php

namespace addons\epay\library;

use epayment\EpayNotify;
use epayment\EpayService;
use epayment\EpaySubmit;
use Exception;
use think\Log;
use think\Response;
use think\Session;
use Yansongda\Pay\Pay;

/**
 * 订单服务类
 *
 * @package addons\epay\library
 */
class Service
{

    public static function submitOrder($amount, $orderid = null, $type = null, $title = null, $notifyurl = null, $returnurl = null, $method = null)
    {
        if (!is_array($amount)) {
            $params = [
                'amount' => $amount,
                'orderid' => $orderid,
                'type' => $type,
                'title' => $title,
                'notifyurl' => $notifyurl,
                'returnurl' => $returnurl,
                'method' => $method,
            ];
        } else {
            $params = $amount;
        }
        $type = isset($params['type']) && in_array($params['type'], ['alipay', 'wechat']) ? $params['type'] : 'wechat';
        $method = isset($params['method']) ? $params['method'] : 'web';
        $orderid = isset($params['orderid']) ? $params['orderid'] : date("YmdHis") . mt_rand(100000, 999999);
        $amount = isset($params['amount']) ? $params['amount'] : 1;
        $title = isset($params['title']) ? $params['title'] : "支付";
        $auth_code = isset($params['auth_code']) ? $params['auth_code'] : '';
        $openid = isset($params['openid']) ? $params['openid'] : '';

        $request = request();
        $notifyurl = isset($params['notifyurl']) ? $params['notifyurl'] : $request->root(true) . '/addons/epay/index/' . $type . 'notify';
        $returnurl = isset($params['returnurl']) ? $params['returnurl'] : $request->root(true) . '/addons/epay/index/' . $type . 'return/out_trade_no/' . $orderid;
        $html = '';
        $config = Service::getConfig($type);
        $config[$type]['notify_url'] = $notifyurl;
        $config[$type]['return_url'] = $returnurl;

        //检测易支付
        if (self::check_way($type)) {
            return self::epaymentSubmit($notifyurl, $returnurl, $type, $amount,$orderid,$title,config('site.name'));
        }

        if ($type == 'alipay') {
            //创建支付对象
            $pay = new Pay($config);
            //支付宝支付,请根据你的需求,仅选择你所需要的即可
            $params = [
                'out_trade_no' => $orderid,//你的订单号
                'total_amount' => $amount,//单位元
                'subject' => $title,
            ];
            //如果是移动端自动切换为wap
            $method = $request->isMobile() ? 'wap' : $method;

            switch ($method) {
                case 'web':
                    //电脑支付,跳转
                    $html = $pay->driver($type)->gateway('web')->pay($params);
                    Response::create($html)->send();
                    break;
                case 'wap':
                    //手机网页支付,跳转
                    $html = $pay->driver($type)->gateway('wap')->pay($params);
                    Response::create($html)->send();
                    break;
                case 'app':
                    //APP支付,直接返回字符串
                    $html = $pay->driver($type)->gateway('app')->pay($params);
                    break;
                case 'scan':
                    //扫码支付,直接返回字符串
                    $html = $pay->driver($type)->gateway('scan')->pay($params);
                    break;
                case 'pos':
                    //刷卡支付,直接返回字符串
                    //刷卡支付必须要有auth_code
                    $params['auth_code'] = $auth_code;
                    $html = $pay->driver($type)->gateway('pos')->pay($params);
                    break;
                default:
                    //其它支付类型请参考：https://docs.pay.yansongda.cn/alipay
            }
        }
        else {
            //如果是PC支付,判断当前环境,进行跳转
            if ($method == 'web') {
                if ((strpos($request->server('HTTP_USER_AGENT'), 'MicroMessenger') !== false)) {
                    Session::delete("openid");
                    Session::set("wechatorderdata", $params);
                    $url = addon_url('epay/api/wechat', [], true, true);
                    header("location:{$url}");
                    exit;
                } elseif ($request->isMobile()) {
                    $method = 'wap';
                }
            }

            //创建支付对象
            $pay = new Pay($config);
            $params = [
                'out_trade_no' => $orderid,//你的订单号
                'body' => $title,
                'total_fee' => $amount * 100, //单位分
            ];
            switch ($method) {
                case 'web':
                    //电脑支付,跳转到自定义展示页面(FastAdmin独有)
                    $html = $pay->driver($type)->gateway('web')->pay($params);
                    Response::create($html)->send();
                    break;
                case 'mp':
                    //公众号支付
                    //公众号支付必须有openid
                    $params['openid'] = $openid;
                    $html = $pay->driver($type)->gateway('mp')->pay($params);
                    break;
                case 'wap':
                    //手机网页支付,跳转
                    $params['spbill_create_ip'] = $request->ip(0, false);
                    $html = $pay->driver($type)->gateway('wap')->pay($params);
                    header("location:{$html}");
                    exit;
                    break;
                case 'app':
                    //APP支付,直接返回字符串
                    $html = $pay->driver($type)->gateway('app')->pay($params);
                    break;
                case 'scan':
                    //扫码支付,直接返回字符串
                    $html = $pay->driver($type)->gateway('scan')->pay($params);
                    break;
                case 'pos':
                    //刷卡支付,直接返回字符串
                    //刷卡支付必须要有auth_code
                    $params['auth_code'] = $auth_code;
                    $html = $pay->driver($type)->gateway('pos')->pay($params);
                    break;
                case 'miniapp':
                    //小程序支付,直接返回字符串
                    //小程序支付必须要有openid
                    $params['openid'] = $openid;
                    $html = $pay->driver($type)->gateway('miniapp')->pay($params);
                    break;
                default:
            }
        }
        //返回字符串
        $html = is_array($html) ? json_encode($html) : $html;
        return $html;
    }

    /**
     * 检测是否为易支付
     * @param string $type
     * @return bool
     */
    public static function check_way($type = 'alipay')
    {
        $config_name = $type . '_way';
        $addon_config = get_addon_config('epay');
        $way = isset($addon_config[$config_name]) ?: 'epay';
        if ($way == 'epay')
            return true;
        else
            return false;
    }

    /**
     * 易支付下单
     * @param string $notify_url
     * @param string $return_url
     * @param string $type
     * @param float $money
     * @param int $out_trade_no
     * @param string $name
     * @param string $sitename
     * @return string
     */
    public static function epaymentSubmit($notify_url = '', $return_url = '', $type = 'aplipay', $money = 0.1, $out_trade_no = 0, $name = 'test', $sitename = 'test')
    {
//        $submit_config = EpayService::getBaseConfig();
//        $epay_config = Service::getConfig('epay')['epay'];
//        $submit_config['apiurl'] = $epay_config['api'];//支付API地址
//        $submit_config['partner'] = $epay_config['pid'];//商户ID
//        $submit_config['key'] = $epay_config['key'];//商户KEY
//        $submit_config['transport'] = request() -> scheme();//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//        var_dump($submit_config);
        $submit_config = self::getEpaymentConf();
//        trace($submit_config);

    //构造要请求的参数数组，无需改动
        $parameter = array(
            "pid" => $submit_config['partner'],
            "type" => $type=='wechat'?'wxpay':$type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "name"	=> $name,
            "money"	=> $money,
            "sitename"	=> $sitename
        );

//        trace($parameter);

        $obj = new EpaySubmit($submit_config);
        $return = $obj->buildRequestForm($parameter);
//        var_dump($return);
        echo $return;
        return $return;

    }

    /**
     * 获取易支付商户配置
     * @return array
     */
    public static function getEpaymentConf()
    {
        $submit_config = EpayService::getBaseConfig();
        $epay_config = Service::getConfig('epay')['epay'];
        $submit_config['apiurl'] = $epay_config['api'];//支付API地址
        $submit_config['partner'] = $epay_config['pid'];//商户ID
        $submit_config['key'] = $epay_config['key'];//商户KEY
        $submit_config['transport'] = request() -> scheme();//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
//        var_dump($submit_config);
        $submit_config = (array)array_map('trim',$submit_config);
        return $submit_config;
    }

    /**
     * 检查易支付回调真实性
     * @param array $data
     * @param string $type
     * @return bool|object
     */
    public static function epaymentNotify($data,$type='alipay')
    {
        if (empty($data))
            return false;

        $data = EpayService::notifyArrayFitter($data);
//        var_dump($data);
//        exit();
        $data['type'] = $type;

        $notify = new EpayNotify(self::getEpaymentConf());
        $result = $notify -> verifyNotify($data);
        if ($result !== true)
        {
            return false;
        }
        else
        {
            $class = (new class ($data,$type)
            {
                public $notify_result = [];

                public $type = 'alipay';

                public function __construct($data,$type='alipay')
                {
                    $this->type = $type;
                    if ($type == 'alipay')
                    {
                        //公共响应参数
                        $this->notify_result['code'] = '10000';//网关返回码
                        $this->notify_result['msg'] = 'Success';//网关返回码描述
                        $this->notify_result['sign'] = $data['sign'];

                        //响应参数
                        $this->notify_result['trade_no'] = $data['trade_no'];//支付宝交易号
                        $this->notify_result['out_trade_no'] = $data['out_trade_no'];//商户订单号
                        $this->notify_result['buyer_logon_id'] = '159****5620';//买家支付宝账号
                        $this->notify_result['total_amount'] = $data['money'];//交易金额
                        $this->notify_result['receipt_amount'] = $data['money'];//实收金额
                        $this->notify_result['gmt_payment'] = datetime(time());//交易支付时间

                        $this->notify_result['fund_bill_list']['fund_channel'] = 'ALIPAYACCOUNT';//交易使用的资金渠道
                        $this->notify_result['fund_bill_list']['amount'] = $data['money'];//该支付工具类型所使用的金额

                        $this->notify_result['buyer_user_id'] = '2088101117955611';//买家在支付宝的用户id

//                        $this->notify_result['notify_type'] = 'trade_status_sync';
//                        $this->notify_result['notify_id'] = '70fec0c2730b27528665af4517c27b95';
//                        $this->notify_result['sign_type'] = $data['sign_type'];
//                        $this->notify_result['subject'] = $data['name'];
//                        $this->notify_result['payment_type'] = 1;
//                        $this->notify_result['trade_status'] = $data['trade_status'];
//                        $this->notify_result['price'] = $data['money'];
//                        $this->notify_result['total_fee'] = $data['money'];
//                        $this->notify_result['quantity'] = 1;
                    }
                    elseif ($type == 'wechat')
                    {
                        //公共响应参数
                        $this->notify_result['return_code'] = 'SUCCESS';//返回状态码

                        $this->notify_result['appid'] = Service::getConfig('wechat')['wechat']['appid'];//小程序ID
                        $this->notify_result['mch_id'] = Service::getConfig('wechat')['wechat']['mch_id'];//商户号
                        $this->notify_result['nonce_str'] = '5K8264ILTKCH16CQ2502SI8ZNMTM67VS';//随机字符串
                        $this->notify_result['sign'] = $data['sign'];//签名
                        $this->notify_result['sign_type'] = $data['sign_type'];//签名类型
                        $this->notify_result['result_code'] = 'SUCCESS';//业务结果
                        $this->notify_result['openid'] = 'wxd930ea5d5a258f4f';//用户在商户appid下的唯一标识
                        $this->notify_result['is_subscribe'] = 'N';//用户是否关注公众账号，Y-关注，N-未关注
                        $this->notify_result['trade_type'] = 'NATIVE';//交易类型
                        $this->notify_result['bank_type'] = 'CMC';//付款银行
                        $this->notify_result['total_fee'] = $data['money']*100;//订单金额
                        $this->notify_result['cash_fee'] = $data['money']*100;//现金支付金额
                        $this->notify_result['transaction_id'] = $data['trade_no'];//微信支付订单号
                        $this->notify_result['out_trade_no'] = $data['out_trade_no'];//商户订单号
                        $this->notify_result['time_end'] = datetime(time(),'YmdHis');//商户订单号
                    }
                }

                public function verify()
                {
//                    return [];
                    return $this->notify_result;
                }

                public function success()
                {
                    if ($this->type == 'wechat') {
                        echo '<xml>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <return_msg><![CDATA[OK]]></return_msg>
</xml>';
                    } else {
                        echo 'success';
                    }
                    return;
                }

            });



            return $class;
        }

    }

    /**
     * 创建支付对象
     * @param string $type 支付类型
     * @param array $config 配置信息
     * @return bool
     */
    public static function createPay($type, $config = [])
    {
        $type = strtolower($type);
        if (!in_array($type, ['wechat', 'alipay'])) {
            return false;
        }
        $config = self::getConfig($type);
        $config = array_merge($config[$type], $config);
        $pay = new Pay($config);
        return $pay;
    }

    /**
     * 验证回调是否成功
     * @param string $type 支付类型
     * @param array $config 配置信息
     * @return bool|Pay
     */
    public static function checkNotify($type, $config = [])
    {
        $type = strtolower($type);
        if (!in_array($type, ['wechat', 'alipay'])) {
            return false;
        }
        try {
            //检测易支付
            if (self::check_way($type)) {
                return self::epaymentNotify(input('',null,'trim'),$type);
            }

            $pay = new Pay(self::getConfig($type));
            $data = $type == 'wechat' ? file_get_contents("php://input") : request()->post('', null, 'trim');

            $data = $pay->driver($type)->gateway()->verify($data);

            if ($type == 'alipay') {
                if (in_array($data['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                    return $pay;
                }
            } else {
                return $pay;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 验证返回是否成功
     * @param string $type 支付类型
     * @param array $config 配置信息
     * @return bool|Pay
     */
    public static function checkReturn($type, $config = [])
    {
        $type = strtolower($type);
        if (!in_array($type, ['wechat', 'alipay'])) {
            return false;
        }
        //微信无需验证
        if ($type == 'wechat') {
            return true;
        }
        try {
            //检测易支付
            if (self::check_way($type)) {
                return self::epaymentNotify(input('',null,'trim'),$type);
            }

            $pay = new Pay(self::getConfig($type));
            $data = $type == 'wechat' ? file_get_contents("php://input") : request()->get('', null, 'trim');
            $data = $pay->driver($type)->gateway()->verify($data);
            if ($data) {
                return $pay;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * 获取配置
     * @param string $type 支付类型
     * @return array|mixed
     */
    public static function getConfig($type = 'wechat')
    {
        $config = get_addon_config('epay');
        $config = isset($config[$type]) ? $config[$type] : $config['wechat'];
        if ($config['log']) {
            $config['log'] = [
                'file' => LOG_PATH . '/epaylogs/' . $type . '-' . date("Y-m-d") . '.log',
                'level' => 'debug'
            ];
        }
        if (isset($config['cert_client']) && substr($config['cert_client'], 0, 6) == '/epay/') {
            $config['cert_client'] = ADDON_PATH . $config['cert_client'];
        }
        if (isset($config['cert_key']) && substr($config['cert_key'], 0, 6) == '/epay/') {
            $config['cert_key'] = ADDON_PATH . $config['cert_key'];
        }

        $config['notify_url'] = empty($config['notify_url']) ? addon_url('epay/api/notifyx', [], false) . '/type/' . $type : $config['notify_url'];
        $config['notify_url'] = !preg_match("/^(http:\/\/|https:\/\/)/i", $config['notify_url']) ? request()->root(true) . $config['notify_url'] : $config['notify_url'];
        $config['return_url'] = empty($config['return_url']) ? addon_url('epay/api/returnx', [], false) . '/type/' . $type : $config['return_url'];
        $config['return_url'] = !preg_match("/^(http:\/\/|https:\/\/)/i", $config['return_url']) ? request()->root(true) . $config['return_url'] : $config['return_url'];
        return [$type => $config];
    }

}