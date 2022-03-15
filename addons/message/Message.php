<?php

namespace addons\message;

use app\common\library\Menu;
use app\common\model\User;
use Exception;
use fast\Date;
use PDOException;
use think\Addons;
use think\Config;
use think\Db;
use think\exception\ValidateException;
use think\Request;
use think\Route;

/**
 * 通知消息管理插件
 */
class Message extends Addons
{
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'message',
                'title'   => '通知消息管理',
                'icon'    => 'fa fa-bullhorn',
                'remark'  => '常用于管理站内通知消息，支持个体消息和系统消息',
                'sublist' => [
                    ['name' => 'message/index', 'title' => '查看'],
                    ['name' => 'message/add', 'title' => '添加'],
                    ['name' => 'message/edit', 'title' => '修改'],
                    ['name' => 'message/del', 'title' => '删除'],
                    ['name' => 'message/multi', 'title' => '批量更新'],
                ]
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('message');
        return true;
    }

    /**
     * 插件启用方法
     */
    public function enable()
    {
        Menu::enable('message');
    }

    /**
     * 插件禁用方法
     */
    public function disable()
    {
        Menu::disable('message');
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
            'actionname'     => $actionname,
            'controllername' => $controllername
        ];
        return $this->fetch('view/hook/user_sidenav_after', $data);
    }

    /**
     * 实现发送消息钩子方法
     *
     * @param 类型 array 参数一的说明 [user_id,message_type,message_title,message_content]
     * @return array 返回类型
     * @example 示例  hook('send_message', [1, 'user', '标题', '内容'])
     * @author  Created by Xing <464401240@qq.com>
     */
    public function sendMessage($params)
    {
        if ($params) {
            list($params['user_id'], $params['message_type'], $params['message_title'], $params['message_content']) = $params;
            $user_id = isset($params['user_id']) ? $params['user_id'] : 0;
            if ($params['message_type'] == 'user' && !$user_id) {
                return false;
            }

            Db::startTrans();
            try {
                $model = new \app\common\model\MessageNotice;
                $result = $model->allowField(true)->save($params);
                if ($params['message_type'] == 'user') {
                    $messageUser = new \app\common\model\MessageUser;
                    $messageUser->save(['user_id' => $user_id, 'message_id' => $model->message_id]);
                }
                Db::commit();
            } catch (ValidateException $e) {
                Db::rollback();
                return false;
            } catch (PDOException $e) {
                Db::rollback();
                return false;
            } catch (Exception $e) {
                Db::rollback();
                return false;
            }
            if ($result !== false) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}
