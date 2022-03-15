<?php

namespace app\admin\controller\qnbuygroup;

use app\common\controller\Backend;

/**
 * 用户组设置管理
 *
 * @icon fa fa-circle-o
 */
class Qngroupset extends Backend
{
    /**
     * Buygroupset模型对象
     * @var \app\admin\model\user\Buygroupset
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\qnbuygroup\Buygroupset;
    }

    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //如果发送的来源是Selectpage，则转发到Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with('group')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with('group')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    public function add()
    {
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), [], ['class' => 'form-control selectpicker']));
        $this->view->assign('expgroupList', build_select('row[expgroup_id]', \app\admin\model\UserGroup::column('id,name'), [], ['class' => 'form-control selectpicker']));
        return parent::add();
    }

    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->view->assign('expgroupList', build_select('row[expgroup_id]', \app\admin\model\UserGroup::column('id,name'), $row['expgroup_id'], ['class' => 'form-control selectpicker']));
        $this->view->assign('groupList', build_select('row[group_id]', \app\admin\model\UserGroup::column('id,name'), $row['group_id'], ['class' => 'form-control selectpicker']));
        return parent::edit($ids);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
}
