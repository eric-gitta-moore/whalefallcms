<?php

namespace app\admin\model\comment;

use think\Model;

class Report extends Model
{
    // 表名
    protected $name = 'comment_report';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'type_text'
    ];


    public function getStatusList()
    {
        return ['settled' => __('Settled'), 'unsettled' => __('Unsettled')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getTypeList()
    {
        $data = [
            1 => '色情低俗',
            2 => '政治敏感',
            3 => '违法暴力',
            4 => '恐怖血腥',
            5 => '非法贩卖',
            6 => '仇恨言论',
            7 => '打小广告',
            8 => '其他'
        ];
        return $data;
    }

    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function article()
    {
        return $this->belongsTo('Article', 'article_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function site()
    {
        return $this->belongsTo('Site', 'site_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function post()
    {
        return $this->belongsTo('Post', 'post_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
