<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model\user;

use app\common\model\cartoon\Cartoon;
use think\Model;


class Buylog extends Model
{

    

    

    // 表名
    protected $name = 'buylog';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['cartoon' => __('漫画'), 'novel' => __('小说'), 'listen' => __('听书')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function cartoon()
    {
        return $this->belongsTo('\app\common\model\cartoon\Cartoon','bookid');
    }

    public function novel()
    {
        return $this->belongsTo('\app\common\model\novel\Novel','bookid');
    }


}
