<?php

return array (
  0 => 
  array (
    'name' => 'markdown',
    'title' => '使用Markdown格式进行评论',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '是否Markdown解析评论内容',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'reportnums',
    'title' => '举报次数阀值',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '超过配置的次数后该评论将待审<br>0表示不启用',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'ippostnums',
    'title' => 'IP发贴限制条数(天)',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '0',
    'rule' => 'required',
    'msg' => '',
    'tip' => '当天评论次数超过配置的次数后IP将禁止发言<br>0表示不限制',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'ippostinterval',
    'title' => 'IP发贴间隔时长(秒)',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '同IP连续两次发贴的间隔时长<br>0表示不启用',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'selfdelete',
    'title' => '删除自己发表的评论',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '会员是否可以自己删除评论,否的话只有管理员能删除',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'sensitive',
    'title' => '敏感词检测和过滤',
    'type' => 'radio',
    'content' => 
    array (
      1 => '开启',
      0 => '关闭',
    ),
    'value' => '1',
    'rule' => 'required',
    'msg' => '',
    'tip' => '是否开启敏感词的检测和过滤',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'sensitivecheck',
    'title' => '敏感词检测方式',
    'type' => 'radio',
    'content' => 
    array (
      'forbidden' => '包含敏感词则发布失败',
      'filter' => '包含敏感词采用过滤的方式',
    ),
    'value' => 'filter',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'sensitivefilterwords',
    'title' => '敏感词过滤替换值',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '***',
    'rule' => 'required',
    'msg' => '',
    'tip' => '相应的敏感词将会被替换成设置的值',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'pagesize',
    'title' => '每页显示的评论数',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '10',
    'rule' => '',
    'msg' => '',
    'tip' => '评论分页大小',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'position',
    'title' => '评论框位置',
    'type' => 'radio',
    'content' => 
    array (
      'top' => '顶部',
      'bottom' => '底部',
    ),
    'value' => 'top',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'defaultsort',
    'title' => '默认排序方式',
    'type' => 'radio',
    'content' => 
    array (
      'new' => '从新到旧',
      'early' => '从旧到新',
      'hot' => '最热门在前',
    ),
    'value' => 'new',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'showtype',
    'title' => '评论展现方式',
    'type' => 'radio',
    'content' => 
    array (
      'floor' => '盖楼的方式',
      'reply' => '回贴的方式(只有一级)',
    ),
    'value' => 'floor',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  12 => 
  array (
    'name' => 'maxlevel',
    'title' => '盖楼回复最大层级',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '5',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  13 => 
  array (
    'name' => 'floorpagesize',
    'title' => '楼中楼分页大小',
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
  14 => 
  array (
    'name' => 'postaudit',
    'title' => '发表审核配置',
    'type' => 'radio',
    'content' => 
    array (
      'enabled' => '全部评论都需要审核',
      'sensitive' => '含有敏感词时需要审核',
      'disabled' => '全部不需要审核',
    ),
    'value' => 'sensitive',
    'rule' => 'required',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  15 => 
  array (
    'name' => 'thirdlogin',
    'title' => '第三方登录',
    'type' => 'checkbox',
    'content' => 
    array (
      'account' => '网站',
      'qq' => 'QQ',
      'weibo' => '微博',
      'wechat' => '微信',
    ),
    'value' => 'account,qq,weibo,wechat',
    'rule' => '',
    'msg' => '',
    'tip' => '开放的登录方式',
    'ok' => '',
    'extend' => '',
  ),
  16 => 
  array (
    'name' => 'formmodule',
    'title' => '表单模块',
    'type' => 'checkbox',
    'content' => 
    array (
      'emoji' => '表情',
      'image' => '图片(暂不可用)',
    ),
    'value' => 'emoji',
    'rule' => '',
    'msg' => '',
    'tip' => '表发评论处的功能模块',
    'ok' => '',
    'extend' => '',
  ),
  17 => 
  array (
    'name' => 'nextpage',
    'title' => '下一页加载方式',
    'type' => 'radio',
    'content' => 
    array (
      'loadmore' => '加载更多',
      'page' => '分页栏模式',
    ),
    'value' => 'loadmore',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
  18 => 
  array (
    'name' => 'defaultemoji',
    'title' => '默认表情',
    'type' => 'select',
    'content' => 
    array (
      'WordPress' => 'WordPress',
      '微博-默认' => '微博-默认',
      '微博-心情' => '微博-心情',
      '微博-浪小花' => '微博-浪小花',
      '微博-拜年' => '微博-拜年',
      '微博-柏夫' => '微博-柏夫',
      '微博-小幺鸡' => '微博-小幺鸡',
      '微博-天气' => '微博-天气',
      '微博-休闲' => '微博-休闲',
      '微博-搞怪' => '微博-搞怪',
      '微博-大熊' => '微博-大熊',
      '微博-BOBO和TOTO' => '微博-BOBO和TOTO',
      '微博-管不着' => '微博-管不着',
      '微博-阿狸' => '微博-阿狸',
      '微博-懒猫猫' => '微博-懒猫猫',
      '微博-大耳兔' => '微博-大耳兔',
      '微博-哈皮兔' => '微博-哈皮兔',
      '微博-星座' => '微博-星座',
      '微博-爱心' => '微博-爱心',
      '微博-亚运会' => '微博-亚运会',
      '微博-张小盒' => '微博-张小盒',
      '微博-悠嘻猴' => '微博-悠嘻猴',
      '微博-小新小浪' => '微博-小新小浪',
      '微博-蘑菇点点' => '微博-蘑菇点点',
    ),
    'value' => '微博-默认',
    'rule' => '',
    'msg' => '',
    'tip' => '',
    'ok' => '',
    'extend' => '',
  ),
);
