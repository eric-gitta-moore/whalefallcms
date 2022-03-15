<?php

namespace addons\invite;

use addons\batchimg\library\batchimg\image\Exception;
use addons\invite\model\Invite as InviteModel;
use app\common\library\Auth;
use app\common\library\Menu;
use app\common\model\User;
use think\Addons;
use think\Cookie;
use think\Db;
use think\Hook;
use think\Request;
use think\View;

/**
 * 邀请插件
 */
class Invite extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {

        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {

        return true;
    }

    /**
     * 会员中心边栏后
     * @return mixed
     * @throws \Exception
     */
    public function userSidenavAfter()
    {
        $request = Request::instance();
        $controllername = strtolower($request->controller());
        $actionname = strtolower($request->action());
        $data = [
            'controllername' => $controllername,
            'actionname'     => $actionname
        ];
        return $this->fetch('view/hook/user_sidenav_after', $data);
    }

    /**
     * 会员注册成功
     * @param $auth
     * @throws \think\exception\DbException
     */
    public function userRegisterSuccessed($auth)
    {
        $inviter = (int)Cookie::get("inviter");
        if ($inviter) {
            $ip = Request::instance()->ip(0, false);
            $config = get_addon_config('invite');
            $inviterUser = User::get($inviter);

            //未找到邀请人，或IP和邀请者相同
            if (!$inviterUser || $inviterUser->loginip == $auth->loginip) {
                return;
            }

            //高级过滤模式
            if ($config['filtermode'] == 'advanced') {
                $ipRegistered = InviteModel::where('user_id', $inviterUser->id)->where('ip', $ip)->find();
                if ($ipRegistered) {
                    return;
                }
            }

            //创建邀请记录
            InviteModel::create(['user_id' => $inviterUser->id, 'ip' => $ip, 'invited_user_id' => $auth->id]);

            //受邀请者增加积分
            if ($config['invitedscore']) {
                User::score($config['invitedscore'], $auth->id, '受邀注册赠送');
            }

            //每日邀请上限
            if ($config['dailymaxinvite']) {
                $inviteCount = InviteModel::where('user_id', $inviterUser->id)->whereTime('createtime', 'today')->count();
                if ($inviteCount > $config['dailymaxinvite']) {
                    return;
                }
            }

            //增加邀请者积分
            User::score($config['rewardscore'], $inviterUser->id, '邀请好友注册');

        }
        Cookie::delete("inviter");
    }

//    public function appEnd()
//    {
//        Cookie::delete("inviter");
//    }

}
