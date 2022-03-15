<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'recharge_order_settled' => 
    array (
      0 => 'agent',
    ),
    'qnbuggroup_order_settled' => 
    array (
      0 => 'agent',
    ),
    'order_settled' => 
    array (
      0 => 'agent',
    ),
    'user_register_successed' => 
    array (
      0 => 'agent',
      1 => 'defaultusergroup',
      2 => 'invite',
    ),
    'config_init' => 
    array (
      0 => 'customizerequirejsconfig',
    ),
    'app_init' => 
    array (
      0 => 'epay',
      1 => 'templates',
    ),
    'user_sidenav_after' => 
    array (
      0 => 'invite',
      1 => 'message',
      2 => 'qnbuygroup',
      3 => 'recharge',
      4 => 'signin',
      5 => 'user',
      6 => 'withdraw',
    ),
    'admin_login_init' => 
    array (
      0 => 'loginbg',
    ),
    'send_message' => 
    array (
      0 => 'message',
    ),
    'sms_send' => 
    array (
      0 => 'smsbao',
    ),
    'sms_notice' => 
    array (
      0 => 'smsbao',
    ),
    'sms_check' => 
    array (
      0 => 'smsbao',
    ),
    'module_init' => 
    array (
      0 => 'templates',
    ),
    'addon_module_init' => 
    array (
      0 => 'templates',
    ),
  ),
  'route' => 
  array (
    '/invite/[:id]$' => 'invite/index/index',
    '/qrcode$' => 'qrcode/index/index',
    '/qrcode/build$' => 'qrcode/index/build',
    '/third$' => 'third/index/index',
    '/third/connect/[:platform]' => 'third/index/connect',
    '/third/callback/[:platform]' => 'third/index/callback',
    '/third/bind/[:platform]' => 'third/index/bind',
    '/third/unbind/[:platform]' => 'third/index/unbind',
  ),
);