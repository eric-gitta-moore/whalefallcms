<?php

namespace addons\comment\model;

use app\common\library\Auth;
use app\common\library\Email;
use think\Exception;
use think\Model;
use think\Validate;

/**
 * 点赞模型
 */
class Like extends Model
{
    protected $name = "comment_like";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    //自定义初始化
    protected static function init()
    {
    }

    /**
     * 关联会员模型
     */
    public function userinfo()
    {
        return $this->belongsTo("app\common\model\User")->field('id,nickname,avatar')->setEagerlyType(1);
    }
}
