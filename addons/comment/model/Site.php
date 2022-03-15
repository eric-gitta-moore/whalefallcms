<?php

namespace addons\comment\model;

use app\common\library\Auth;
use app\common\library\Email;
use think\Exception;
use think\Model;
use think\Validate;

/**
 * 站点模型
 */
class Site extends Model
{
    protected $name = "comment_site";
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
}
