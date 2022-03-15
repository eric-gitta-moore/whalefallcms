<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model\novel;

use think\Model;


class Relation extends Model
{

    

    

    // 表名
    protected $name = 'novel_relation';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];


    public function novel()
    {
        return $this->belongsTo('Novel','novel_novel_id');
    }

    public function cate()
    {
        return $this->belongsTo('\app\common\model\config\Cate','config_cate_id');
    }






}
