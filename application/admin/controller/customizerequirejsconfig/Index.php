<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\customizerequirejsconfig;

use app\common\controller\Backend;


/**
 *
 */
class Index extends Backend
{

    protected $noNeedRight = ['index'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index()
    {
        return "恭喜你创建插件成功";
    }



}
