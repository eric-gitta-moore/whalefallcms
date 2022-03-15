<?php

return array (
  0 => 
  array (
    'name' => 'wechat',
    'title' => '微信',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'appid' => '',
      'app_id' => '',
      'app_secret' => '',
      'miniapp_id' => '',
      'mch_id' => '',
      'key' => '',
      'notify_url' => '/addons/epay/api/notifyx/type/wechat',
      'cert_client' => '/epay/certs/apiclient_cert.pem',
      'cert_key' => '/epay/certs/apiclient_key.pem',
      'log' => 1,
    ),
    'rule' => '',
    'msg' => '',
    'tip' => '微信参数配置',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'alipay',
    'title' => '支付宝',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'app_id' => '',
      'notify_url' => '/addons/epay/api/notifyx/type/alipay',
      'return_url' => '/addons/epay/api/returnx/type/alipay',
      'ali_public_key' => '',
      'private_key' => '',
      'log' => 1,
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '支付宝参数配置',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'epay',
    'title' => '易支付',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      'api' => 'http://pay.hackwl.cn/',
      'pid' => '637383',
      'key' => 'D2D0796209EA886DD4575372C9129E30',
      'notify_url' => '/addons/epay/api/notifyx/type/epay',
      'return_url' => '/addons/epay/api/returnx/type/epay',
      'log' => '1',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '易支付参数配置',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'alipay_way',
    'title' => '支付宝通道选择',
    'type' => 'select',
    'content' => 
    array (
      'epay' => '易支付',
      'alipay' => '官方',
    ),
    'value' => 'epay',
    'rule' => 'required',
    'msg' => '',
    'tip' => '支付宝支付通道选择',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'wechat_way',
    'title' => '微信通道选择',
    'type' => 'select',
    'content' => 
    array (
      'epay' => '易支付',
      'wechat' => '官方',
    ),
    'value' => 'epay',
    'rule' => 'required',
    'msg' => '',
    'tip' => '微信支付通道选择',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => '请注意微信支付证书路径位于/addons/epay/certs目录下，请替换成你自己的证书<br>
appid：APP的appid<br>
app_id：公众号的appid<br>
app_secret：公众号的secret<br>
miniapp_id：小程序ID<br>
mch_id：微信商户ID<br>
key：微信商户支付的密钥',
    'rule' => '',
    'msg' => '',
    'tip' => '微信参数配置',
    'ok' => '',
    'extend' => '',
  ),
);
