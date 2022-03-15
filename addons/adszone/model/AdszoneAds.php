<?php

namespace addons\adszone\model;

use think\Model;

class AdszoneAds extends Model
{

    // 表名
    protected $name = 'adszone_ads';
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    protected static function _initialize()
    {
        parent::_initialize();
    }
}
