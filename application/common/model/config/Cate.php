<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model\config;

use think\Model;
use traits\model\SoftDelete;

class Cate extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'cate';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['disable' => __('Status disable'), 'cartoon' => __('Status cartoon'), 'novel' => __('Status novel'), 'listen' => __('Status listen')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function cartoon()
    {
        return $this->belongsToMany('app\common\model\cartoon\Cartoon','cartoon_relation','cartoon_cartoon_id','config_cate_id');
    }

    public function novel()
    {
        return $this->belongsToMany('app\common\model\novel\novel','novel_relation','novel_novel_id','config_cate_id');
    }

//    public function listen()
//    {
//        return $this->belongsToMany('app\common\model\listen\listen','listen_relation','listen_listen_id','config_cate_id');
//    }


}
