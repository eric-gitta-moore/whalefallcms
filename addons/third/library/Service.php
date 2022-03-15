<?php

namespace addons\third\library;

use addons\third\model\Third;
use app\common\model\User;
use fast\Random;
use think\Db;
use think\exception\PDOException;

/**
 * 第三方登录服务类
 *
 * @author Karson
 */
class Service
{

    /**
     * 第三方登录
     * @param string $platform 平台
     * @param array  $params   参数
     * @param array  $extend   会员扩展信息
     * @param int    $keeptime 有效时长
     * @return boolean
     */
    public static function connect($platform, $params = [], $extend = [], $keeptime = 0)
    {
        $time = time();
        $values = [
            'platform'      => $platform,
            'openid'        => $params['openid'],
            'openname'      => isset($params['userinfo']['nickname']) ? $params['userinfo']['nickname'] : '',
            'access_token'  => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'expires_in'    => $params['expires_in'],
            'logintime'     => $time,
            'expiretime'    => $time + $params['expires_in'],
        ];
        $auth = \app\common\library\Auth::instance();

        $auth->keeptime($keeptime);
        $third = Third::get(['platform' => $platform, 'openid' => $params['openid']]);
        if ($third) {
            $user = User::get($third['user_id']);
            if (!$user) {
                return false;
            }
            $third->save($values);
            return $auth->direct($user->id);
        } else {
            // 先随机一个用户名,随后再变更为u+数字id
            $username = Random::alnum(20);
            $password = Random::alnum(6);
            $domain = request()->host();

            Db::startTrans();
            try {
                // 默认注册一个会员
                $result = $auth->register($username, $password, $username . '@' . $domain, '', $extend, $keeptime);
                if (!$result) {
                    return false;
                }
                $user = $auth->getUser();
                $fields = ['username' => 'u' . $user->id, 'email' => 'u' . $user->id . '@' . $domain];
                if (isset($params['userinfo']['nickname'])) {
                    $fields['nickname'] = $params['userinfo']['nickname'];
                }
                if (isset($params['userinfo']['avatar'])) {
                    $fields['avatar'] = htmlspecialchars(strip_tags($params['userinfo']['avatar']));
                }

                // 更新会员资料
                $user = User::get($user->id);
                $user->save($fields);

                // 保存第三方信息
                $values['user_id'] = $user->id;
                Third::create($values);
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $auth->logout();
                return false;
            }

            // 写入登录Cookies和Token
            return $auth->direct($user->id);
        }
    }
}
