<?php

namespace addons\qnbuygroup\model;

use think\Exception;
use think\Model;
use app\common\library\Auth;
use app\common\model\User;

class Buygroupuser extends Model
{
    // 表名
    protected $name = 'qnbuygroup_usergroup';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    public function groupset()
    {
        return $this->belongsTo('app\admin\model\qnbuygroup\Buygroupset', 'group_id', 'id', [], 'LEFT');
    }
}