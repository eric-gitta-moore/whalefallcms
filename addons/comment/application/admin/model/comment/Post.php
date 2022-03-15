<?php

namespace app\admin\model\comment;

use think\exception\PDOException;
use think\Model;

class Post extends Model
{
    // 表名
    protected $name = 'comment_post';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'deletetime_text',
        'status_text'
    ];

    public static function init()
    {
        parent::init();
        self::afterDelete(function ($row) {
            try {
                if ($row['status'] != 'deleted') {
                    $row->article()->setDec('comments');
                }
                if ($row['pid'] > 0) {
                    Post::get($row['pid'])->setDec('comments');
                }
            } catch (PDOException $e) {

            }

        });
    }

    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden'), 'deleted' => __('Deleted')];
    }

    public function getDeletetimeTextAttr($value, $data)
    {
        $value = $value ? $value : isset($data['deletetime']) ? $data['deletetime'] : 0;
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : $data['status'];
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setDeletetimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function article()
    {
        return $this->belongsTo('Article', 'article_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function site()
    {
        return $this->belongsTo('Site', 'site_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function user()
    {
        return $this->belongsTo('\app\admin\model\User', 'user_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
