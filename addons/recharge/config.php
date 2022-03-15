<?php

return array (
  0 => 
  array (
    'name' => 'rechargetips',
    'title' => '充值提示文字',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => '余额可用于购买商品或用于商城消费',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'moneylist',
    'title' => '充值金额列表',
    'type' => 'array',
    'content' => 
    array (
    ),
    'value' => 
    array (
      '￥10' => '10',
      '￥20' => '20',
      '￥30' => '30',
      '￥50' => '50',
      '￥100' => '100',
    ),
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'defaultmoney',
    'title' => '默认充值金额',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'minmoney',
    'title' => '最低充值金额',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0.1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '最低的充值金额',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'iscustommoney',
    'title' => '是否开启任意金额',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
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
  6 => 
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
  7 => 
  array (
    'name' => 'ordercreatelimit',
    'title' => '订单生成限制',
    'type' => 'radio',
    'content' => 
    array (
      1 => '限制',
      0 => '不限制',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '是否限制未支付相同金额的订单在30分钟内只生成一次',
    'ok' => '',
    'extend' => '',
  ),
);
