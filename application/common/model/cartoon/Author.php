<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model\cartoon;

use think\Model;
use traits\model\SoftDelete;

class Author extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'cartoon_author';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];


    public function cartoon()
    {
        return $this->hasMany('Cartoon','cartoon_author_id');
    }







}
