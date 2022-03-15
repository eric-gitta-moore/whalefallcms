<?php

namespace app\admin\model\comment;

use think\Model;

class Article extends Model
{
    // 表名
    protected $name = 'comment_article';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function site()
    {
        return $this->belongsTo('Site', 'site_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
