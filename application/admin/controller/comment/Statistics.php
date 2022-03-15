<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\comment;

use app\admin\model\comment\Post;
use app\admin\model\comment\Site;
use app\admin\model\comment\Article;
use app\admin\model\comment\Report;
use app\common\controller\Backend;
use think\Db;

/**
 * 统计管理
 *
 * @icon fa fa-bar-chart
 * @remark 可以查看评论相关统计信息
 */
class Statistics extends Backend
{

    /**
     * 模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 查询统计
     */
    public function index()
    {
        $date = $this->request->post('date', '');
        $data = $this->getOrderStatisticsData($date);
        $statistics = ['columns' => array_keys($data), 'data' => array_values($data)];
        if ($this->request->isPost()) {
            $this->success('', '', $statistics);
        }
        $statistics['totalcomment'] = intval(Post::where('1=1')->count());
        $statistics['totalsite'] = intval(Site::where('1=1')->count());
        $statistics['totalarticle'] = intval(Article::where('1=1')->count());
        $statistics['totalreport'] = intval(Report::where('status', 'unsettled')->count());
        $statistics['todaycomment'] = intval(Post::whereTime('createtime', 'today')->count());
        $statistics['yesterdaycomment'] = intval(Post::whereTime('createtime', 'yesterday')->count());
        $statistics['todaysite'] = intval(Site::whereTime('createtime', 'today')->count());
        $statistics['todayarticle'] = intval(Article::whereTime('createtime', 'today')->count());
        $statistics['todayreport'] = intval(Report::whereTime('createtime', 'today')->where('status', 'unsettled')->count());
        $this->view->assign('statistics', $statistics);
        $this->assignconfig('statistics', $statistics);
        return $this->view->fetch();
    }

    /**
     * 获取订单统计数据
     * @param string $date
     * @return array
     */
    protected function getOrderStatisticsData($date = '')
    {
        if ($date) {
            list($start, $end) = explode(' - ', $date);

            $starttime = strtotime($start);
            $endtime = strtotime($end);
        } else {
            $starttime = \fast\Date::unixtime('day', 0, 'begin');
            $endtime = \fast\Date::unixtime('day', 0, 'end');
        }
        $totalseconds = $endtime - $starttime;

        $format = '%Y-%m-%d';
        if ($totalseconds > 86400 * 30 * 2) {
            $format = '%Y-%m';
        } else if ($totalseconds > 86400) {
            $format = '%Y-%m-%d';
        } else {
            $format = '%H:00';
        }
        Db::execute('set sql_mode =\'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION\';');
        $orderlist = Post::where('createtime', 'between time', [$starttime, $endtime])
            ->field('createtime, status, COUNT(*) AS nums, MIN(createtime) AS min_createtime, MAX(createtime) AS max_createtime, 
            DATE_FORMAT(FROM_UNIXTIME(createtime), "' . $format . '") AS comment_date')
            ->group('comment_date')
            ->select();

        if ($totalseconds > 84600 * 30 * 2) {
            $starttime = strtotime('last month', $starttime);
            while (($starttime = strtotime('next month', $starttime)) <= $endtime) {
                $column[] = date('Y-m', $starttime);
            }
        } else if ($totalseconds > 86400) {
            for ($time = $starttime; $time <= $endtime;) {
                $column[] = date("Y-m-d", $time);
                $time += 86400;
            }
        } else {
            for ($time = $starttime; $time <= $endtime;) {
                $column[] = date("H:00", $time);
                $time += 3600;
            }
        }
        $list = array_fill_keys($column, 0);
        foreach ($orderlist as $k => $v) {
            $list[$v['comment_date']] = $v['nums'];
        }
        return $list;

    }


}
