<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\user\controller\user;

use app\common\controller\Userend;

/**
 * 个人资料
 */
class Profile extends Userend
{

    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->auth->level) $this->assign('user', $this->auth->getLevel());
        return $this->view->fetch('user/profile');
    }

}
