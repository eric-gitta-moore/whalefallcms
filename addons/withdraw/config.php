<?php

return array (
  0 => 
  array (
    'name' => 'withdrawtips',
    'title' => '提现提示文字',
    'type' => 'text',
    'content' => 
    array (
    ),
    'value' => '你可以将余额提现至支付宝账户',
    'rule' => 'required',
    'msg' => '',
    'tip' => '用于显示在提现页面的文字',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'minmoney',
    'title' => '最低提现金额',
    'type' => 'number',
    'content' => 
    array (
    ),
    'value' => '1',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'monthlimit',
    'title' => '每月可提现次数',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '为0时表示不限制提现次数',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => '__tips__',
    'title' => '温馨提示',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1.使用此插件前请务必安装<a href="https://www.fastadmin.net/store/epay.html" target="_blank">微信支付宝</a>整合插件，并配置好支付宝相关配置信息<br>2.后台处理提现时只支持通过企业支付宝向用户转账',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
