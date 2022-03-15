<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\index\controller;

use app\common\controller\BaseFrontend;
use app\common\controller\Frontend;
use fast\Date;
use think\Db;
use think\Exception;
use think\exception\PDOException;

class Signin extends BaseFrontend
{
    protected $layout = 'user';
    protected $noNeedRight = ["*"];

    /**
     * 签到首页
     * @return string
     */
    public function index()
    {
        $config = get_addon_config('signin');
        $signdata = $config['signinscore'];
        $date = $this->request->request('date', date("Y-m-d"), "trim");
        $time = strtotime($date);

        $lastdata = \addons\signin\model\Signin::where('user_id', $this->auth->id)->order('createtime', 'desc')->find();
        $successions = $lastdata && $lastdata['createtime'] > Date::unixtime('day', -1) ? $lastdata['successions'] : 0;
        $signin = \addons\signin\model\Signin::where('user_id', $this->auth->id)->whereTime('createtime', 'today')->find();

        $calendar = new \addons\signin\library\Calendar();
        $list = \addons\signin\model\Signin::where('user_id', $this->auth->id)
            ->field('id,createtime')
            ->whereTime('createtime', 'between', [date("Y-m-1", $time), date("Y-m-1", strtotime("+1 month", $time))])
            ->select();
        foreach ($list as $index => $item) {
            $calendar->addEvent(date("Y-m-d", $item->createtime), date("Y-m-d", $item->createtime), "", false, "signed");
        }
        $this->assignconfig('fillupscore', $config['fillupscore']);
        $this->assignconfig('isfillup', $config['isfillup']);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('date', $date);
        $this->view->assign('successions', $successions);
        $successions++;
        $score = isset($signdata['s' . $successions]) ? $signdata['s' . $successions] : $signdata['sn'];
        $this->view->assign('signin', $signin);
        $this->view->assign('score', $score);
        $this->view->assign('signinscore', $config['signinscore']);
        $this->view->assign('title', "每日签到");
        return $this->view->fetch();
    }

    /**
     * 立即签到
     */
    public function dosign()
    {
        if ($this->request->isPost()) {
            $config = get_addon_config('signin');
            $signdata = $config['signinscore'];

            $lastdata = \addons\signin\model\Signin::where('user_id', $this->auth->id)->order('createtime', 'desc')->find();
            $successions = $lastdata && $lastdata['createtime'] > Date::unixtime('day', -1) ? $lastdata['successions'] : 0;
            $signin = \addons\signin\model\Signin::where('user_id', $this->auth->id)->whereTime('createtime', 'today')->find();
            if ($signin) {
                $this->error('今天已签到,请明天再来!');
            } else {
                $successions++;
                $score = isset($signdata['s' . $successions]) ? $signdata['s' . $successions] : $signdata['sn'];
                Db::startTrans();
                try {
                    \addons\signin\model\Signin::create(['user_id' => $this->auth->id, 'successions' => $successions, 'createtime' => time()]);
                    \app\common\model\User::score($score, $this->auth->id, "连续签到{$successions}天");
                    Db::commit();
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error('签到失败,请稍后重试');
                }
                $this->success('签到成功!连续签到' . $successions . '天!获得' . $score . '积分');
            }
        }
        $this->error("请求错误");
    }

    /**
     * 签到补签
     */
    public function fillup()
    {
        $date = $this->request->request('date');
        $time = strtotime($date);
        $config = get_addon_config('signin');
        if (!$config['isfillup']) {
            $this->error('暂未开启签到补签');
        }
        if ($time > time()) {
            $this->error('无法补签未来的日期');
        }
        if ($config['fillupscore'] > $this->auth->score) {
            $this->error('你当前积分不足');
        }
        $days = Date::span(time(), $time, 'days');
        if ($config['fillupdays'] < $days) {
            $this->error("只允许补签{$config['fillupdays']}天的签到");
        }
        $count = \addons\signin\model\Signin::where('user_id', $this->auth->id)
            ->where('type', 'fillup')
            ->whereTime('createtime', 'between', [Date::unixtime('month'), Date::unixtime('month', 0, 'end')])
            ->count();
        if ($config['fillupnumsinmonth'] <= $count) {
            $this->error("每月只允许补签{$config['fillupnumsinmonth']}次");
        }
        Db::name('signin')->whereTime('createtime', 'd')->select();
        $signin = \addons\signin\model\Signin::where('user_id', $this->auth->id)
            ->where('type', 'fillup')
            ->whereTime('createtime', 'between', [$date, date("Y-m-d 23:59:59", $time)])
            ->count();
        if ($signin) {
            $this->error("该日期无需补签到");
        }
        $successions = 1;
        $prev = $signin = \addons\signin\model\Signin::where('user_id', $this->auth->id)
            ->whereTime('createtime', 'between', [date("Y-m-d", strtotime("-1 day", $time)), date("Y-m-d 23:59:59", strtotime("-1 day", $time))])
            ->find();
        if ($prev) {
            $successions = $prev['successions'] + 1;
        }
        Db::startTrans();
        try {
            \app\common\model\User::score(-$config['fillupscore'], $this->auth->id, '签到补签');
            //寻找日期之后的
            $nextList = \addons\signin\model\Signin::where('user_id', $this->auth->id)
                ->where('createtime', '>=', strtotime("+1 day", $time))
                ->order('createtime', 'asc')
                ->select();
            foreach ($nextList as $index => $item) {
                //如果是阶段数据，则中止
                if ($index > 0 && $item->successions == 1) {
                    break;
                }
                $day = $index + 1;
                if (date("Y-m-d", $item->createtime) == date("Y-m-d", strtotime("+{$day} day", $time))) {
                    $item->successions = $successions + $day;
                    $item->save();
                }
            }
            \addons\signin\model\Signin::create(['user_id' => $this->auth->id, 'type' => 'fillup', 'successions' => $successions, 'createtime' => $time + 43200]);
            Db::commit();
        } catch (PDOException $e) {
            Db::rollback();
            $this->error('补签失败,请稍后重试');
        } catch (Exception $e) {
            Db::rollback();
            $this->error('补签失败,请稍后重试');
        }

        $this->success('补签成功');
    }

    /**
     * 排行榜
     */
    public function rank()
    {
        $data = \addons\signin\model\Signin::with(["user"])
            ->where("createtime", ">", Date::unixtime('day', -1))
            ->field("user_id,MAX(successions) AS days")
            ->group("user_id")
            ->order("days", "desc")
            ->limit(10)
            ->select();
        foreach ($data as $index => $datum) {
            $datum->getRelation('user')->visible(['id', 'username', 'nickname', 'avatar']);
        }
        $this->success("", "", ['ranklist' => collection($data)->toArray()]);
    }

}
