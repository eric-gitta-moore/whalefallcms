<?php

return [
    [
        'name'    => 'signinscore',
        'title'   => '签到积分',
        'type'    => 'array',
        'content' => [],
        'value'   => [
            's1' => '1',
            's2' => '2',
            's3' => '3',
            's4' => '4',
            's5' => '5',
            's6' => '6',
            's7' => '7',
            'sn' => '7',
        ],
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '签到赠送的积分',
        'ok'      => '',
        'extend'  => ''
    ],
    [
        'name'    => 'isfillup',
        'title'   => '是否开启补签',
        'type'    => 'radio',
        'content' => [
            1 => '是',
            0 => '否',
        ],
        'value'   => 1,
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '是否开启补签',
        'ok'      => '',
        'extend'  => ''
    ],
    [
        'name'    => 'fillupscore',
        'title'   => '补签消耗积分',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => 100,
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '补签时消耗的积分',
        'ok'      => '',
        'extend'  => ''
    ],
    [
        'name'    => 'fillupdays',
        'title'   => '补签天数内',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => 3,
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '多少天数内漏签的可以补签',
        'ok'      => '',
        'extend'  => ''
    ],
    [
        'name'    => 'fillupnumsinmonth',
        'title'   => '每月可补签次数',
        'type'    => 'number',
        'content' => [
        ],
        'value'   => 1,
        'rule'    => 'required',
        'msg'     => '',
        'tip'     => '每月可补签次数',
        'ok'      => '',
        'extend'  => ''
    ],
];
