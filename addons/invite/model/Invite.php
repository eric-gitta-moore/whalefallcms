<?php

namespace addons\invite\model;

use think\Model;

class Invite extends Model
{

    // 表名
    protected $name = 'invite';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    public function user()
    {
        return $this->belongsTo('\app\common\model\User', 'user_id', 'id');
    }

    public function invited()
    {
        return $this->belongsTo('\app\common\model\User', 'invited_user_id', 'id');
    }
}
