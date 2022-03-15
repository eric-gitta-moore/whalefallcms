<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\user;

use app\common\controller\Backend;

/**
 * 用户日志
 *
 * @icon fa fa-circle-o
 */
class Log extends Backend
{

    /**
     * Log模型对象
     * @var \app\common\model\user\Log
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\user\Log;

    }

}
