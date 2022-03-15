<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 会员积分变动管理
 *
 * @icon fa fa-circle-o
 */
class ScoreLog extends Userend
{

    /**
     * Log模型对象
     * @var \app\common\model\user\Scorelog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\user\Scorelog;

    }

}
