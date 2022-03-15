<?php

return array (
  0 => 
  array (
    'name' => 'buygrouptips',
    'title' => '提示文字',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => '购买会员可享受更多网站特权',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'paytypelist',
    'title' => '支付方式',
    'type' => 'checkbox',
    'content' => 
    array (
      'wechat' => '微信支付',
      'alipay' => '支付宝支付',
    ),
    'value' => 'wechat,alipay',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'defaultpaytype',
    'title' => '默认支付方式',
    'type' => 'radio',
    'content' => 
    array (
      'wechat' => '微信支付',
      'alipay' => '支付宝支付',
    ),
    'value' => 'alipay',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
