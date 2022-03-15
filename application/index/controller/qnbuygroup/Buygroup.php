<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\index\controller\qnbuygroup;

use addons\qnbuygroup\model\Buygrouporder;
use app\common\controller\BaseFrontend;
use app\common\controller\Frontend;
use app\common\model\User;
use think\Cookie;
use think\Exception;
use app\admin\model\qnbuygroup\Buygroupset;
use addons\qnbuygroup\model\Buygroupuser;
use think\Hook;
use app\common\library\Menu;

class Buygroup extends BaseFrontend
{

    protected $layout = 'user';
    protected $noNeedLogin = ['pay', 'epay'];
    protected $noNeedRight = ['*'];

    protected $model = null;

    public function _initialize()
    {
        $this->model = new Buygroupuser;
        parent::_initialize();
    }


    /**vip购买页面
     * @return string
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function buy()
    {
        $usergroup = Buygroupuser::where("user_id", $this->auth->id)->where("expiredtime", ">", time())->order("createtime desc")->find();

        $config = get_addon_config('qnbuygroup');
        $vipsettingList = Buygroupset::all();
        $paytypeList = [];
        foreach (explode(',', $config['paytypelist']) as $index => $item) {
            $paytypeList[] = ['value' => $item, 'image' => '/assets/addons/qnbuygroup/img/' . $item . '.jpg', 'default' => $item === $config['defaultpaytype']];
        }

        $defaultvipset = null;
        if ($vipsettingList) {
            $defaultvipset = $vipsettingList[0];
        }

        foreach ($vipsettingList as $k => $v) {
            $v['description'] = explode(',', $v['description']);
            if ($usergroup && $usergroup['group_id'] == $v['group_id']) {
                $defaultvipset = $v;
            }
        }


        $this->view->assign('paytypeList', $paytypeList);
        $this->view->assign("vipsetList", $vipsettingList);
        $this->view->assign("defaultvipset", $defaultvipset);
        $this->view->assign('addonConfig', $config);
        $this->view->assign('title', '用户组购买');
        return $this->view->fetch();
    }

    /**提交订单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function submit()
    {
        $buygroupid = $this->request->request('buygroupid');
        $paytype = $this->request->request('paytype');
        if ($buygroupid <= 0) {
            $this->error('用户组不能为空');
        }
        $buygroudset = Buygroupset::where('id', $buygroupid)->find();
        if (!$buygroudset) {
            $this->error('无效的用户组');
        }
        if (!$paytype || !in_array($paytype, ['alipay', 'wechat'])) {
            $this->error("支付类型不能为空");
        }
        try {
            \addons\qnbuygroup\model\Buygrouporder::submitOrder($buygroudset, $paytype ? $paytype : 'wechat');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return;
    }


    /**
     * 处理回调
     * @throws \think\exception\DbException
     */
    public function epay()
    {
        $type = $this->request->param('type');
        $paytype = $this->request->param('paytype');
        if ($type == 'notify') {
            $pay = \addons\epay\library\Service::checkNotify($paytype);
            if (!$pay) {
                echo '签名错误';
                return;
            }
            $data = $pay->verify();
            try {
                $payamount = $paytype == 'alipay' ? $data['total_amount'] : $data['total_fee'] / 100;
                \addons\qnbuygroup\model\Buygrouporder::settle($data['out_trade_no'], $payamount);
            } catch (Exception $e) {
            }
            echo $pay->success();
        } else {
            $pay = \addons\epay\library\Service::checkReturn($paytype);
            if (!$pay) {
                $this->error('签名错误');
            }
            //微信支付没有返回链接
            if ($pay === true) {
                $this->success("请返回网站查看支付状态!", "");
            }

            //你可以在这里定义你的提示信息,但切记不可在此编写逻辑
            $this->success("恭喜你！充值成功!", url("user/index"));
        }
        return;
    }
}