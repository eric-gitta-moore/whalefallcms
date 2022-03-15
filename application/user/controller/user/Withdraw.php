<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\user\controller\user;

use addons\recharge\model\MoneyLog;
use app\common\controller\Userend;
use think\Exception;
use think\Validate;
use think\Db;

/**
 *
 */
class Withdraw extends Userend
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
     * 余额提现
     * @return string
     */
    public function index()
    {
        $config = get_addon_config('withdraw');
        $this->view->assign('addonConfig', $config);
        $this->view->assign('title', __('Withdraw'));
        return $this->view->fetch();
    }

    /**
     * 提交提现申请
     * @throws \think\exception\DbException
     */
    public function submit()
    {
        $money = $this->request->request('money');
        $account = $this->request->request('account');
        $type = 'alipay';

        $token = $this->request->post('__token__');

        //验证Token
        if (!Validate::is($token, "token", ['__token__' => $token])) {
            $this->error("Token验证错误，请重试！", '', ['__token__' => $this->request->token()]);
        }

        //刷新Token
        $this->request->token();

        if ($money <= 0) {
            $this->error('提现金额不正确');
        }
        if ($money > $this->auth->money) {
            $this->error('提现金额超出可提现额度');
        }
        if (!$account) {
            $this->error("提现账户不能为空");
        }
        if (!Validate::is($account, "email") && !Validate::is($account, "/^1\d{10}$/")) {
            $this->error("提现账户只能是手机号或Email");
        }

        $config = get_addon_config('withdraw');
        if (isset($config['minmoney']) && $money < $config['minmoney']) {
            $this->error('提现金额不能低于' . $config['minmoney'] . '元');
        }
        if ($config['monthlimit']) {
            $count = \addons\withdraw\model\Withdraw::where('user_id', $this->auth->id)->whereTime('createtime', 'month')->count();
            if ($count >= $config['monthlimit']) {
                $this->error("已达到本月最大可提现次数");
            }
        }
        Db::startTrans();
        try {
            $data = [
                'user_id' => $this->auth->id,
                'money'   => $money,
                'type'    => $type,
                'account' => $account
            ];
            \addons\withdraw\model\Withdraw::create($data);
            \app\common\model\User::money(-$money, $this->auth->id, "提现");
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success("提现申请成功！请等待后台审核！", url("user.withdrawlog/index"));
        return;
    }
}
