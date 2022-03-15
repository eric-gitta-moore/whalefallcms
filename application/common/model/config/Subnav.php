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

class Subnav extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'subnav';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'category_text',
        'switch_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getCategoryList()
    {
        return ['cartoon' => __('Category cartoon'), 'novel' => __('Category novel'), 'listen' => __('Category listen')];
    }

    public function getSwitchList()
    {
        return ['0' => __('Switch 0'), '1' => __('Switch 1')];
    }


    public function getCategoryTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['category']) ? $data['category'] : '');
        $list = $this->getCategoryList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSwitchTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['switch']) ? $data['switch'] : '');
        $list = $this->getSwitchList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
