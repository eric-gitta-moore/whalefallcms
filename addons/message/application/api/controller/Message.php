<?php

namespace app\api\controller;

use app\common\controller\Api;

/**
 * 站内消息接口
 */
class Message extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = '*';
    // Child模型对象
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\MessageUser;
    }

    /**
     * 站内消息列表
     *
     * @param string $token   Token
     * @param integer $page 页码
     */
    public function getlist()
    {
        $user = $this->auth->getUser();
        $this->success('返回成功', $this->model->getList($user->id));
    }

    /**
     * 未读消息数
     *
     * @param string $token   Token
     */
    public function getUnreadCount()
    {
        $user = $this->auth->getUser();
        $this->success('返回成功', $this->model->getUnreadCount($user->id));
    }

    /**
     * 站内消息详情
     *
     * @param string $token   Token
     * @param integer $rec_id 消息ID
     */
    public function getMessageDetails()
    {
        $user = $this->auth->getUser();
        $rec_id = $this->request->request('rec_id');
        $this->success('返回成功', $this->model->getMessageDetails($rec_id));
    }

    /**
     * 设置消息已读
     *
     * @param string $token   Token
     * @param integer $rec_id 消息ID
     */
    public function setMessageForRead()
    {
        $user = $this->auth->getUser();
        $rec_id = $this->request->request('rec_id');
        if ($this->model->getUnreadCount($rec_id, $user->id)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除消息
     *
     * @param string $token   Token
     * @param integer $rec_id 消息ID
     */
    public function deletedMessage()
    {
        $user = $this->auth->getUser();
        $rec_id = $this->request->request('rec_id');
        if ($this->model->deletedMessage($rec_id, $user->id)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}