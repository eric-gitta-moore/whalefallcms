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

class Cartoon extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'cartoon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [
        'status_text',
        'log_time_text',
        'free_type_text',
        'vip_type_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('连载'), '1' => __('完结')];
    }

    public function getFreeTypeList()
    {
        return ['0' => __('免费'), '1' => __('收费')];
    }

    public function getVipTypeList()
    {
        return ['0' => __('Vip_type 0'), '1' => __('Vip_type 1')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLogTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['log_time']) ? $data['log_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFreeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['free_type']) ? $data['free_type'] : '');
        $list = $this->getFreeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getVipTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['vip_type']) ? $data['vip_type'] : '');
        $list = $this->getVipTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setLogTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function author()
    {
        return $this->belongsTo('Author','cartoon_author_id');
    }

    public function chapter()
    {
        return $this->hasMany('Chapter','cartoon_cartoon_id') -> order('weigh');
    }

    public function cate()
    {
        return $this->belongsToMany('app\common\model\config\Cate','cartoon_relation','config_cate_id','cartoon_cartoon_id');
    }

    public function collection()
    {
        return $this->hasMany('app\common\model\user\Collection','bookid');
    }

    public function userCollection($user_id)
    {
        return $this->hasMany('app\common\model\user\Collection','bookid')
                    -> where('user_id',$user_id);
    }

    public function reward()
    {
        return $this->hasMany('app\common\model\user\Reward','bookid');
    }


    public function buyLog()
    {
        return $this->morphMany('\app\common\model\user\Buylog',['status','bookid'],'cartoon');
    }
}
