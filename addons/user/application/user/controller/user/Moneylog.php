<?php

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 会员积分变动管理
 *
 * @icon fa fa-circle-o
 */
class Moneylog extends Userend
{

    /**
     * Log模型对象
     * @var \app\common\model\user\Scorelog
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $recharge = get_addon_info('recharge');
        if ($recharge) {
            if ($recharge['state']) {
                $this->model = new \addons\recharge\model\MoneyLog;
            } else {
                $this->error("请在插件管理中切换会员充值余额插件状态为启用！");
            }
        } else {
            $this->error("请先在后台安装配置会员充值余额插件");
        }

    }

}
