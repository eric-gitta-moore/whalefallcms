<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\cartoon;

use app\common\controller\Backend;
use think\Db;
use think\Exception;

/**
 * 漫画管理
 *
 * @icon fa fa-circle-o
 */
class Cartoon extends Backend
{
    
    /**
     * Cartoon模型对象
     * @var \app\common\model\cartoon\Cartoon
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\cartoon\Cartoon;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("freeTypeList", $this->model->getFreeTypeList());
        $this->view->assign("vipTypeList", $this->model->getVipTypeList());
    }

    /**
     * 批量设置付费
     * @return \think\response\View
     */
    public function batch()
    {
        $type = 'cartoon';
        if ($this->request -> isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                $params = $this->preExcludeFields($params);

                Db::startTrans();
                try {
                    $prefix = config('database.prefix');
                    Db::execute('update ' . $prefix . $type . ' set `free_type`=:freetype,`vip_type`=:viptype,`start_pay`=:startpay',[
//                        'tab' => $prefix . $type,
                        'freetype' => $params['free_type'],
                        'viptype' => $params['vip_type'],
                        'startpay' => $params['start_pay'],
                    ]);
                    Db::execute('update ' . $prefix . $type . '_chapter' . ' set `money`=:money',[
//                        'tab' => $prefix . $type . '_chapter',
                        'money' => $params['money'],
                    ]);
                    Db::commit();
                    $this->success();
                }
                catch (Exception $exception)
                {
                    $this->error('数据库写入失败:' . $exception -> getMessage());
                    Db::rollback();
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return view();
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
