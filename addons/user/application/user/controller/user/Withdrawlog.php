<?php

namespace app\user\controller\user;

use addons\recharge\model\MoneyLog;
use app\common\controller\Userend;
use think\Exception;

/**
 *
 */
class Withdrawlog extends Userend
{
    protected $noNeedLogin = [];
    protected $noNeedRight = ['*'];

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $recharge = get_addon_info('withdraw');
        if (!$recharge) {
            $this->error("请先在后台安装配置会员余额提现插件");
        } else {
            if (!$recharge['state']) {
                $this->error("请在插件管理中切换会员余额提现插件状态为启用！");
            }
        }
    }

    /**
     * 提现日志
     * @return string
     */
    public function index()
    {
        $withdrawloglist = \addons\withdraw\model\Withdraw::where(['user_id' => $this->auth->id])
            ->order('id desc')
            ->paginate(10);

        $this->view->assign('title', __('Withdraw log'));
        $this->view->assign('withdrawloglist', $withdrawloglist);
        return $this->view->fetch();
    }
}
