<?php


namespace epayment;


class EpayService
{
    public static function getBaseConfig()
    {

        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //商户ID
        $alipay_config['partner']		= '637383';

        //商户KEY
        $alipay_config['key']			= 'D2D0796209EA886DD4575372C9129E30';


        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';

        //支付API地址
        $alipay_config['apiurl']    = 'http://pay.hackwl.cn/';

        return $alipay_config;
    }

    public static function notifyArrayFitter($arr)
    {
        $real = [
            'pid' => isset($arr['pid'])?$arr['pid']:'',
            'trade_no' => isset($arr['trade_no'])?$arr['trade_no']:'',
            'out_trade_no' => isset($arr['out_trade_no'])?$arr['out_trade_no']:'',
            'type' => isset($arr['type'])?$arr['type']:'',
            'name' => isset($arr['name'])?$arr['name']:'',
            'money' => isset($arr['money'])?$arr['money']:'',
            'trade_status' => isset($arr['trade_status'])?$arr['trade_status']:'',
            'sign' => isset($arr['sign'])?$arr['sign']:'',
            'sign_type' => isset($arr['sign_type'])?$arr['sign_type']:'',
        ];
        return $real;
    }
}