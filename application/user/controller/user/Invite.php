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

/**
 *
 */
class Invite extends Userend
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $recharge = get_addon_info('invite');
        if (!$recharge) {
            $this->error("请先在后台安装配置会员邀请好友插件");
        } else {
            if (!$recharge['state']) {
                $this->error("请在插件管理中切换会员邀请好友插件状态为启用！");
            }
        }
    }

    public function index()
    {
        $inviteList = \addons\invite\model\Invite::
        where(['user_id' => $this->auth->id])
            ->with('invited')
            ->order('id desc')
            ->paginate(10);

        $inviteConfig = get_addon_config('invite');
        $this->view->assign('title', "邀请好友");
        $this->view->assign('inviteList', $inviteList);
        $this->view->assign('inviteConfig', $inviteConfig);
        return $this->view->fetch();
    }

}
