<?php

namespace app\user\controller\user;

use app\common\controller\Userend;
use think\Config;
use think\Validate;

/**
 * 会员中心入口文件
 */
class Changepwd extends Userend
{

    /**
     * 修改密码
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $oldpassword = $this->request->post("oldpassword");
            $newpassword = $this->request->post("newpassword");
            $renewpassword = $this->request->post("renewpassword");
            $token = $this->request->post('__token__');
            $rule = [
                'oldpassword' => 'require|length:6,30',
                'newpassword' => 'require|length:6,30',
                'renewpassword' => 'require|length:6,30|confirm:newpassword',
                '__token__' => 'token',
            ];

            $msg = [
            ];
            $data = [
                'oldpassword' => $oldpassword,
                'newpassword' => $newpassword,
                'renewpassword' => $renewpassword,
                '__token__' => $token,
            ];
            $field = [
                'oldpassword' => __('Old password'),
                'newpassword' => __('New password'),
                'renewpassword' => __('Renew password')
            ];
            $validate = new Validate($rule, $msg, $field);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return false;
            }

            $ret = $this->auth->changepwd($newpassword, $oldpassword);
            if ($ret) {
                $synchtml = '';
                ////////////////同步到Ucenter////////////////
                if (defined('UC_STATUS') && UC_STATUS) {
                    $uc = new \addons\ucenter\library\client\Client();
                    $synchtml = $uc->uc_user_synlogout();
                }
                $this->success(__('Reset password successful') . $synchtml, url('user/login'));
            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }
        $this->view->assign('title', __('Change password'));
        return $this->view->fetch('user/changepwd');
    }

}
