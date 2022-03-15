<?php

namespace app\common\model\agent;

use think\Model;
use traits\model\SoftDelete;

class Profit extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'agent_profit';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







}
