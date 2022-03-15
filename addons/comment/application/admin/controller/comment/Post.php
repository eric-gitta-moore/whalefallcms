<?php

namespace app\admin\controller\comment;

use app\common\controller\Backend;

/**
 * 评论内容管理
 *
 * @icon fa fa-circle-o
 */
class Post extends Backend
{

    /**
     * Post模型对象
     * @var \app\admin\model\comment\Post
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\comment\Post;
        $this->view->assign("statusList", $this->model->getStatusList());
    }


    /**
     * 查看
     */
    public function index()
    {
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['site', 'article', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['site', 'article', 'user'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            foreach ($list as $index => &$item) {
                $item['content'] = mb_substr(strip_tags($item['content']), 0, 20);
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
