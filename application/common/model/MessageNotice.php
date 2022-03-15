<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model;

use think\Model;

class MessageNotice extends Model
{

    // 表名
    protected $name = 'message_notice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'message_type_text'
    ];

    
    public function getMessageTypeList()
    {
        return ['system' => __('System'), 'user' => __('User')];
    }


    public function getMessageTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['message_type']) ? $data['message_type'] : '');
        $list = $this->getMessageTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }
}
