<?php

return [
    array(
        'name'    => 'rewardscore',
        'title'   => '邀请者奖励积分',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '10',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '被邀请者注册账号成功后邀请者奖励积分',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'invitedscore',
        'title'   => '被邀请者赠送积分',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '10',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '被邀请者注册账号成功将获得积分',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'dailymaxinvite',
        'title'   => '每天邀请限制',
        'type'    => 'string',
        'content' =>
            array(),
        'value'   => '0',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '每天邀请的前几名赠送积分，后面的不赠送积分，0为不限制',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'filtermode',
        'title'   => '过滤模式',
        'type'    => 'select',
        'content' =>
            array(
                'basic'    => '基础模式(不判断IP)',
                'advanced' => '高级模式(判断已邀请注册的IP,如有相同IP忽略邀请记录)',
            ),
        'value'   => 'basic',
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
    array(
        'name'    => 'rewrite',
        'title'   => '伪静态',
        'type'    => 'array',
        'content' =>
            array(),
        'value'   =>
            array(
                'index/index' => '/invite/[:id]$',
            ),
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '',
        'ok'      => '',
        'extend'  => '',
    ),
];
