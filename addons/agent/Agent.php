<?php

namespace addons\agent;

use addons\agent\library\Service;
use addons\recharge\model\MoneyLog;
use app\common\library\Menu;
use app\common\model\User;
use think\Addons;
use think\Cookie;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * 插件
 */
class Agent extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [];
        $config_file = ADDON_PATH . "agent" . DS . 'config' . DS . "menu.php";
        if (is_file($config_file)) {
            $menu = include $config_file;
        }
        if ($menu) {
            Menu::create($menu);
        }
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        $info = get_addon_info('agent');
        Menu::delete(isset($info['first_menu']) ? $info['first_menu'] : 'agent');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        $info = get_addon_info('agent');
        Menu::enable(isset($info['first_menu']) ? $info['first_menu'] : 'agent');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        $info = get_addon_info('agent');
        Menu::disable(isset($info['first_menu']) ? $info['first_menu'] : 'agent');
    }

    public function rechargeOrderSettled(&$param)
    {
        $this->orderSettled($param->payamount, $param->user_id, $param);
    }

    public function qnbuggroupOrderSettled(&$param)
    {
        $this->orderSettled($param->payamount, $param->user_id, $param);
    }

    /**
     * 扣量操作
     * @param float $order_money 实付金额
     * @param int $user_id 购买者ID
     * @param bool $order_model
     * @throws DbException
     * @throws Exception
     */
    public function orderSettled($order_money, $user_id, $order_model = false)
    {
        $user = User::get($user_id);
        $agent_conf = get_addon_config('agent');
        $system_deduction = $agent_conf['agent_deduction'];//系统默认扣量设置
        $distribution_map = Service::getDistributionMap();

        //解析代理路径
        $path = Service::parse($user->agent_path);

        //删除N级分销之外的代理
        //比如：代理路径 0,1,2,3,4,5,6,7,8,9
        //分销级别：4
        //仅保留后四位代理，即 6,7,8,9
        $active_path = Service::getActivePath($path);

        //判断是否有上级代理
        if (!empty($active_path)) {
            //有上级代理

            $active_path = array_reverse($active_path);

            Db::startTrans();
            try {

                $agent_level = 1;
                foreach ($active_path as $item) {
                    //判断该代理是否扣量
                    $agent_user = User::get($item);
                    if (empty($agent_user))
                        continue;

                    //该代理的扣量识标
                    $deduction = $agent_user->deduction;
                    //扣量设置，0:默认,-1:不扣量,其它:自定义设置
                    $deduction_conf = $agent_user->deduction_conf;

                    $flag = 0;//实际扣量设置
                    if ($deduction_conf == 0) {
                        $flag = $system_deduction;//系统设置
                    } elseif ($deduction_conf == -1) {
                        $flag = false;//不扣量
                    } else {
                        $flag = $deduction_conf;//自定义设置
                    }

//                    dump($deduction + 1);
//                    dump($flag);
//                    dump($deduction + 1 == $flag);
                    if ($deduction + 1 == $flag) {
                        //扣量，扣量识标归零，不加钱
//                        $agent_user->setField('deduction', 0);
                        $agent_user -> save([
                            'deduction' => 0
                        ]);

                        //标记扣量
//                        $order_model->setField('hidden', 1);
                        $order_model -> save(['hidden' => 1]);
                    } else {
                        //不扣量，扣量识标+1，加钱
                        $agent_user->setInc('deduction');

                        $add_money = $order_money * $distribution_map[$agent_level] / 100;
                        $add_money = round($add_money, 2);

                        MoneyLog::insert([
                            'user_id' => $agent_user -> id,
                            'money' => $add_money,
                            'before' => $agent_user -> money,
                            'after' => $agent_user -> money + $add_money,
                            'memo' => '代理分润',
                            'time' => time()
                        ]);

                        $agent_user->setInc('money', $add_money);
                    }

                    ++$agent_level;
                }


                Db::commit();
            } catch (Exception $exception) {
                Db::rollback();
            }
            //写入日志

        } else {
            //无上级代理，忽略
        }
    }


    public function userRegisterSuccessed(&$user_model)
    {
        $inviter = (int)Cookie::get('inviter');
//        if (empty($inviter))
//        {
//            if (class_exists('\addons\invite\model\Invite'))
//            {
//                $invite = \addons\invite\model\Invite::where([
//                    'invited_user_id' => $user_model -> id,
//                ]) -> whereTime('createtime','today') -> find();
//                $inviter = $invite -> user_id;
//            }
//        }

        if (!empty($inviter)) {
            $inviter_user_model = User::get($inviter);
//            var_dump($user_model -> id);exit();
//            $user_model -> agent_path = $inviter_user_model -> agent_path . $inviter_user_model -> id . ',';
            $r = $user_model->save([
                'agent_path' => $inviter_user_model->agent_path . $inviter_user_model->id . ','
            ]);
//            var_dump($r);
        }
    }
}
