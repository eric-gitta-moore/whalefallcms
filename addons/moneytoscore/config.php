<?php

return array (
  0 => 
  array (
    'type' => 'string',
    'name' => 'score_name',
    'title' => '积分昵称',
    'value' => '书币',
    'content' => '',
    'tip' => '',
    'rule' => 'required',
    'extend' => '',
  ),
  1 => 
  array (
    'type' => 'number',
    'name' => 'cny_to_score',
    'title' => '积分汇率',
    'value' => '100',
    'content' => '',
    'tip' => '',
    'rule' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'type' => 'string',
    'name' => '__tips__',
    'title' => '温馨提示',
    'value' => '积分汇率：1金额对应多少积分<br />前台提示：{$cny_to_score}表示汇率，{$score_name}表示积分昵称 <br>键名随意，只要不重复就行',
    'content' => '',
    'tip' => '',
    'rule' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'type' => 'number',
    'name' => 'min_money',
    'title' => '最低兑换余额',
    'value' => '0.1',
    'content' => '',
    'tip' => '',
    'rule' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'type' => 'array',
    'name' => 'tips_list',
    'title' => '前台提示',
    'value' => 
    array (
      's1' => '1、人民币/金币的汇率为：1元={$cny_to_score}书币',
      's2' => '2、{$score_name}属于虚拟商品，一经购买概不退换。',
      's3' => '3、若充值遇到任何问题，可以联系我们的客服。',
    ),
    'content' => 
    array (
      'value1' => 'title1',
      'value2' => 'title2',
    ),
    'tip' => '',
    'rule' => '',
    'extend' => '',
  ),
);
