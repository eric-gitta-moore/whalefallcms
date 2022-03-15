<?php

namespace addons\withdraw\model;

use think\Model;

/**
 * 提现模型
 */
class Withdraw extends Model
{

    // 表名
    protected $name = 'withdraw';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
    ];

    public function getSettledmoneyAttr($value, $data)
    {
        return max(0, sprintf("%.2f", $data['money'] - $data['handingfee'] - $data['taxes']));
    }
}
