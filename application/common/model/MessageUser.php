<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\common\model;

use think\Model;
use think\Db;

class MessageUser extends Model
{
    // 表名
    protected $name = 'message_user';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function messagenotice()
    {
        return $this->belongsTo('MessageNotice', 'message_id', 'message_id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 消息列表
     *
     * @param 类型 $user_id 用户id
     * @return array 返回类型
     * @example 示例
     * @author Created by xing <lx@xibuts.cn>
     */
    public function getList($user_id, $type = '')
    {
        $this->checkUserMessage($user_id);
        $where = array(
            'user_id' => $user_id,
            'deleted' => 0
        );
        if ($type) {
            $where['message_type'] = $type;
        }
        $list = $this->with(['messagenotice'])->where($where)->order('createtime DESC')->paginate();
        foreach ($list as $row) {
            $row->visible(['rec_id','user_id','message_id','is_see','deleted','createtime']);
            $row->visible(['messagenotice']);
            $row->getRelation('messagenotice')->visible(['message_type', 'message_title', 'message_content']);
        }
        return $list;
    }

    /**
     * 获取用户未查看的消息个数
     * @return array
     */
    public function getUnreadCount($user_id)
    {
        $where = array(
            'user_id' => $user_id,
            'is_see' => 0,
            'deleted' => 0
        );
        // 通知消息未查看数
        return $this->where($where)->count();
    }


    /**
     * 获取用户通知消息详情
     * @param $rec_id | UserMessage.rec_id
     * @param $type | 消息类型
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getMessageDetails($rec_id)
    {
        $where = ['rec_id' => $rec_id];
        $info = $this->with(['messagenotice'])->where($where)->find();
        if ($info) {
            $info->visible(['rec_id','user_id','message_id','is_see','deleted','createtime']);
            $info->visible(['messagenotice']);
            $info->getRelation('messagenotice')->visible(['message_type', 'message_title', 'message_content']);

            if ($info && $info['is_see'] == 0) {
                $this->setMessageForRead($info['rec_id'], $info['user_id']);//设置消息已读
            }
        }
        return $info;
    }

    /**
     * 设置用户消息已读
     * @param $rec_id |数组多条|指定某个|空的则全部
     * @return array
     */
    public function setMessageForRead($rec_id, $user_id)
    {
        $result = false;
        if (!empty($user_id)) {
            $data['is_see'] = 1;
            $set_where['user_id'] = $user_id;

            if (strpos($rec_id, ',')) {
                $rec_id = explode(',', $rec_id);
                $set_where['rec_id'] = ['in',$rec_id];
            } elseif (!empty($rec_id)) {
                $set_where['rec_id'] = $rec_id;
            }
            $result = $this->where($set_where)->update($data);
        }
        return $result;
    }


    /**
     * 删除消息
     * @param $rec_id |数组多条|指定某个|空的则全部
     * @return array
     */
    public function deletedMessage($rec_id, $user_id)
    {
        $result = false;
        if (!empty($user_id)) {
            $data['deleted'] = 1;
            $set_where['user_id'] = $user_id;
            if (strpos($rec_id, ',')) {
                $rec_id = explode(',', $rec_id);
                $set_where['rec_id'] = ['in',$rec_id];
            } elseif (!empty($rec_id)) {
                $set_where['rec_id'] = $rec_id;
            } else {
                // 清空消息
            }
            $result = $this->where($set_where)->update($data);
        }
        return $result;
    }

    /**
     * 查询系统全体消息，如有将其插入用户信息表
     * @param $user_id
     */
    private function checkUserMessage($user_id)
    {
        static $fun = false;
        if ($fun) return; // 防止重复调用
        $fun = true;
        $user_info = Db::name('user')->where('id', $user_id)->find();
        if ($user_info) {
            // 通知
            $user_message = $this->where(array('user_id' => $user_info['id']))->select();
            $message_where = array(
                'message_type' => 'system',
                'createtime' => array('gt', $user_info['createtime']),
            );
            if (!empty($user_message)) {
                $user_id_array = $this->get_arr_column($user_message, 'message_id');
                $message_where['message_id'] = array('NOT IN', $user_id_array);
            }
            $message_notice_no_read = Db::name('message_notice')->field('message_id')->order('createtime ASC')->where($message_where)->select();
            foreach ($message_notice_no_read as $key) {
                $this->save(['user_id' => $user_info['id'], 'message_id' => $key['message_id']]);
            }
        }
    }
    
    /**
     * 获取数组中的某一列
     * @param array $arr 数组
     * @param string $key_name  列名
     * @return array  返回那一列的数组
     */
    private function get_arr_column($arr, $key_name)
    {
        $arr2 = array();
        foreach ($arr as $key => $val) {
            $arr2[] = $val[$key_name];
        }
        return $arr2;
    }
}
