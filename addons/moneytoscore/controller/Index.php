<?php


namespace addons\moneytoscore\controller;


use think\addons\Controller;
use think\Db;
use think\Exception;
use think\Validate;

class Index extends Controller
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    public function submit()
    {
        $money = $this->request->request('money');
        $token = $this->request->post('__token__');
        //验证Token
        if (!Validate::is($token, "token", ['__token__' => $token])) {
            $this->error("Token验证错误，请重试！", '', ['__token__' => $this->request->token()]);
        }

        //刷新Token
        $this->request->token();


        if ($money <= 0) {
            $this->error('兑换金额不正确');
        }
        if ($money > $this->auth->money) {
            $this->error('兑换金额超出余额');
        }

        $config = get_addon_config('moneytoscore');
        if (isset($config['min_money']) && $money < $config['min_money']) {
            $this->error('兑换金额不能低于' . $config['min_money'] . '元');
        }


        Db::startTrans();
        try {
            \app\common\model\User::money(-$money, $this->auth->id, "兑换" . $config['score_name']);
            \app\common\model\User::score(number_format($money * $config['cny_to_score'],2), $this->auth->id, "兑换" . $config['score_name']);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success("兑换成功！", url("index/exchange/exchange"));
        return;
    }
}