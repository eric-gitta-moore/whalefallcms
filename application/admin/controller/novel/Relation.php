<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\novel;

use app\common\controller\Backend;

/**
 * 小说ID-类型关系管理
 *
 * @icon fa fa-circle-o
 */
class Relation extends Backend
{
    
    /**
     * Relation模型对象
     * @var \app\common\model\novel\Relation
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\novel\Relation;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                -> with('novel,cate')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

//            $list = [];
            foreach ($list as &$item) {
                $item['novel_novel_name'] = $item['novel']['name'];
                $item['config_cate_name'] = $item['cate']['name'];
                unset($item['novel']);
                unset($item['cate']);
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    

}
