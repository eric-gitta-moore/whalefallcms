<?php

namespace app\user\controller\user;

use addons\recharge\model\MoneyLog;
use app\common\controller\Userend;
use think\Exception;

/**
 *
 */
class Recharge extends Userend
{
    //protected $layout = 'default';
    protected $noNeedLogin = ['pay', 'epay'];
    protected $noNeedRight = ['*'];

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $recharge = get_addon_info('recharge');
        if (!$recharge) {
            $this->error("请先在后台安装配置会员充值余额插件");
        } else {
            if (!$recharge['state']) {
                $this->error("请在插件管理中切换会员充值余额插件状态为启用！");
            }
        }
    }

    /**
     * 充值余额
     * @return string
     */
    public function index()
    {
        $config = get_addon_config('recharge');
        $moneyList = [];
        foreach ($config['moneylist'] as $index => $item) {
            $moneyList[] = ['value' => $item, 'text' => $index, 'default' => $item === $config['defaultmoney']];
        }

        $paytypeList = [];
        foreach (explode(',', $config['paytypelist']) as $index => $item) {
            $paytypeList[] = ['value' => $item, 'image' => '/assets/addons/recharge/img/' . $item . '.png', 'default' => $item === $config['defaultpaytype']];
        }
        $this->view->assign('addonConfig', $config);
        $this->view->assign('moneyList', $moneyList);
        $this->view->assign('paytypeList', $paytypeList);
        $this->view->assign('title', __('Recharge'));
        return $this->view->fetch();
    }

}
