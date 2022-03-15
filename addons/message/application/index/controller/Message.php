<?php

namespace app\index\controller;

use app\common\controller\Frontend;

/**
 * 站内消息
 */
class Message extends Frontend
{

    protected $layout = 'default';
    protected $noNeedRight = ["*"];
    // Child模型对象
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\MessageUser;
        $messageNotice = new \app\common\model\MessageNotice;
        $this->view->assign("messageTypeList", $messageNotice->getMessageTypeList());
    }

    /**
     * 站内消息列表
     *
     * @param string  $token Token
     * @param integer $page  页码
     */
    public function index()
    {
        $type = $this->request->request('type', 'system');
        $user = $this->auth->getUser();
        //消息列表
        $list = $this->model->getList($user->id, $type);
        $this->view->assign('list', $list);
        $this->view->assign('title', "站内消息");
        return $this->view->fetch();
    }


    /**
     * 站内消息详情
     *
     * @param string  $token  Token
     * @param integer $rec_id 消息ID
     */
    public function details($rec_id = null)
    {
        $row = $this->model->getMessageDetails($rec_id);
        $this->view->assign('row', $row);
        $this->view->assign('title', "消息详情");
        return $this->view->fetch();
    }
}
