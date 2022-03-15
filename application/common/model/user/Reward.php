<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model\user;

use think\Model;


class Reward extends Model
{

    

    

    // 表名
    protected $name = 'reward_log';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text',
        'create_time_text',
    ];
    

    
    public function getStatusList()
    {
        return ['cartoon' => __('Status cartoon'), 'novel' => __('Status novel'), 'listen' => __('Status listen')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getCreateTimeTextAttr($value,$data)
    {
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
//        halt(datetime($value));
        return datetime($value);
    }

    public function user()
    {
        return $this->belongsTo('app\common\model\User');
    }


}
