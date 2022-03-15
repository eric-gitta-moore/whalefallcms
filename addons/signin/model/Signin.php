<?php

namespace addons\signin\model;

use think\Model;

class Signin extends Model
{

    // 表名
    protected $name = 'signin';
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
        return $this->belongsTo('\app\common\model\User', "user_id", "id");
    }

}
