<?php

namespace addons\comment\controller;

use addons\comment\library\Markdown;
use addons\comment\library\SensitiveHelper;
use addons\comment\model\Article;
use addons\comment\model\Like;
use addons\comment\model\Post;
use addons\comment\model\Report;
use addons\comment\model\Site;
use think\addons\Controller;
use think\Config;
use think\Cookie;
use think\exception\PDOException;
use think\Hook;
use think\Lang;
use think\Validate;

class Api extends Controller
{

    /**
     * @var \addons\comment\model\Site
     */
    protected $site = null;

    /**
     * @var \addons\comment\model\Article
     */
    protected $article = null;
    protected $config = [];
    protected $noNeedLogin = ['report', 'comment', 'config', 'login'];

    public function _initialize()
    {
        if ($this->request->action() != 'login') {
            Config::set('default_return_type', 'json');

            $origin = $this->request->server('HTTP_ORIGIN', '');
            header("Access-Control-Allow-Origin:$origin");
            header("Access-Control-Allow-Credentials:true");
            header("Access-Control-Allow-Headers:Origin, Accept-Language, Accept-Encoding, X-Forwarded-For, Connection, Accept, User-Agent, Host, Referer, Cookie, Content-Type, Cache-Control, If-Modified-Since, *");
        }
        parent::_initialize();

        $this->auth->setAllowFields(['id', 'nickname', 'avatar', 'url']);

        $this->config = get_addon_config('comment');
        $auth = $this->auth;
        //监听注册登录注销的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });

        $name = $this->request->request('name');
        $site = Site::where('name', $name)->cache(true)->find();
        if (!$site || $site['status'] == 'hidden') {
            $this->error('站点配置不正确');
        }
        $this->site = $site;

        $key = $this->request->request('key');
        if (!is_numeric($key)) {
            $key = preg_match("/^(http|https)/", $key) ? md5($key) : $key;
        }
        $this->article = Article::where('site_id', $this->site->id)->where('key', $key)->find();
        if ($this->article && $this->article['status'] == 'hidden') {
            $this->error('暂时不可查看');
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success('退出成功');
    }

    /**
     * 举报反馈
     */
    public function report()
    {
        $id = $this->request->request('id');
        $type = $this->request->request('type');
        $content = $this->request->request('content');
        $commentPost = Post::get($id);
        if (!$type) {
            $this->error('请选择举报的类型');
        }
        if (!$commentPost || $commentPost['deletetime']) {
            $this->error('未找到相关评论');
        }
        $data = [
            'site_id'    => $commentPost['site_id'],
            'article_id' => $this->article->id,
            'post_id'    => $commentPost['id'],
            'user_id'    => $this->auth->id,
            'type'       => $type,
            'content'    => $content,
            'ip'         => $this->request->ip(),
            'status'     => 'unsettled',
        ];
        Report::create($data);
        $this->success('反馈成功');
    }

    /**
     * 点赞
     */
    public function like()
    {
        $id = $this->request->request('id');
        $commentPost = Post::get($id);
        if (!$commentPost || $commentPost['deletetime']) {
            $this->error('未找到相关评论');
        }
        $exist = Like::where(['site_id' => $this->site->id, 'article_id' => $this->article->id, 'post_id' => $commentPost['id'], 'user_id' => $this->auth->id])->find();
        if ($exist) {
            $this->error('已经点过赞了，请不要重复点赞');
        }
        $data = [
            'site_id'    => $commentPost['site_id'],
            'article_id' => $this->article->id,
            'post_id'    => $commentPost['id'],
            'user_id'    => $this->auth->id,
            'ip'         => $this->request->ip(),
        ];
        try {
            Like::create($data);
        } catch (PDOException $e) {
            $this->error('点赞失败');
        }
        $commentPost->setInc('likes');
        $this->success('点赞成功');
    }

    /**
     * 配置信息
     */
    public function config()
    {
        $key = $this->request->request('key');
        if (!is_numeric($key)) {
            $key = preg_match("/^(http|https)/", $key) ? md5($key) : $key;
        }
        $data = [
            'config'   => [
                'showType'      => $this->config['showtype'],
                'sort'          => $this->config['defaultsort'],
                'pageSize'      => (int)$this->config['pagesize'],
                'maxLevel'      => (int)$this->config['maxlevel'],
                'floorPagesize' => (int)$this->config['floorpagesize'],
                'position'      => $this->config['position'],
                'selfDelete'    => (int)$this->config['selfdelete'],
                'thirdLogin'    => (array)explode(',', $this->config['thirdlogin']),
                'formModule'    => (array)explode(',', $this->config['formmodule']),
                'nextPage'      => $this->config['nextpage'],
                'defaultEmoji'  => $this->config['defaultemoji'],
                'key'           => $key,
                'author'        => '官方',
                'token'         => $this->request->token(),
            ],
            'userinfo' => $this->auth->isLogin() ? $this->auth->getUserinfo() : null
        ];
        $this->success('', null, $data);
    }

    /**
     * 删除评论
     */
    public function delete()
    {
        $id = $this->request->request('id');
        $commentPost = Post::get($id);
        if ($this->auth->id != 1) {
            if (!$commentPost || $commentPost['user_id'] != $this->auth->id) {
                $this->error('无法进行越权操作');
            }
            if (!$this->config['selfdelete']) {
                $this->error('当前不允许删除评论');
            }
        }

        $commentPost->deletetime = time();
        $commentPost->status = 'deleted';
        $commentPost->save();
        //删除所有子评论
        Post::where('pid', $commentPost->id)->update(['deletetime' => time(), 'status' => 'deleted']);
        //更新父评论评论数量
        if ($commentPost->pid) {
            (new Post())->get($commentPost->pid)->setDec('comments');
        }
        $this->success('删除成功');
    }

    /**
     * 发表评论
     */
    public function create()
    {
        $pid = $this->request->request('pid');
        $content = $this->request->request('content');
        $token = $this->request->request('__token__');
        $ip = $this->request->ip();

        $rule = [
            'content'   => 'require',
            '__token__' => 'require|token',
        ];
        $msg = [
            'content'   => '评论内容不能为空',
            '__token__' => '发表评论过快,请稍后重试',
        ];
        $data = [
            'content'   => $content,
            '__token__' => $token,
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($data);
        if (!$result) {
            $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
        }

        //同IP发贴条数限制
        if ($this->config['ippostnums']) {
            $postNums = Post::where(['ip' => $ip])->whereTime('createtime', 'today')->count();
            if ($postNums >= $this->config['ippostnums']) {
                $this->error("发表评论超过每天的限制", null, ['token' => $this->request->token()]);
            }
        }
        //查找最后评论
        if ($this->config['ippostinterval']) {
            $lastComment = Post::where(['ip' => $ip])->order('id', 'desc')->find();
            if ($lastComment && time() - $lastComment['createtime'] < intval($this->config['ippostinterval'])) {
                //$this->error("对不起！您发表评论的速度过快！请稍微休息一下，喝杯咖啡", null, ['token' => $this->request->token()]);
            }
            //判断评论是否相同
            if ($lastComment && $lastComment['content'] == $content) {
                $this->error("您可能连续了相同的评论，请不要重复提交", null, ['token' => $this->request->token()]);
            }
        }
        //敏感词检测
        $isLegal = true;
        if ($this->config['sensitive']) {
            // 敏感词过滤
            $wordFilePath = ADDON_PATH . 'comment' . DS . 'data' . DS . 'words.txt';
            $handle = SensitiveHelper::init()->setTreeByFile($wordFilePath);
            //首先检测是否合法
            $isLegal = $handle->islegal($content);
            if (!$isLegal) {
                if ($this->config['sensitivecheck'] == 'forbidden') {
                    $this->error('你发表的评论中有非法词汇，请移除后再提交', null, ['token' => $this->request->token()]);
                } else {
                    $content = $handle->replace($content, $this->config['sensitivefilterwords']);
                }
            }
        }
        //评论审核控制
        $status = 'normal';
        if ($this->config['postaudit'] == 'enabled') {
            $status = 'hidden';
        } else if ($this->config['postaudit'] == 'sensitive' && !$isLegal) {
            $status = 'hidden';
        }
        if (!$this->article) {
            //如果未找到文章，先创建一篇文章
            $key = $this->request->request('key');
            $title = $this->request->request('title');
            $url = $this->request->request('url');
            $data = [
                'site_id'  => $this->site->id,
                'key'      => $key ? $key : md5($url),
                'title'    => $title,
                'url'      => $url,
                'comments' => 0,
                'status'   => 'normal',
            ];
            try {
                $this->article = Article::create($data);
            } catch (PDOException $e) {
                $this->error('发表评论失败', null, ['token' => $this->request->token()]);
            }

        }
        $content = Post::emoji($content);
        $content = $this->config['markdown'] ? Markdown::text($content) : $content;
        $data = [
            'site_id'    => $this->site->id,
            'article_id' => $this->article->id,
            'pid'        => $pid,
            'user_id'    => $this->auth->id,
            'ip'         => $ip,
            'useragent'  => substr($this->request->server('HTTP_USER_AGENT'), 0, 255),
            'content'    => $content,
            'status'     => $status
        ];
        try {
            $comment = Post::create($data);
            $data = $comment->toArray();
            if ($pid) {
                $parent = Post::get($pid);
                $parent && $parent->setInc('comments');
            }
            $this->article->setInc('comments');
        } catch (PDOException $e) {
            $this->error('发表评论失败');
        }
        $comment = Post::with(['userinfo'])->where('id', $comment->id)->field('id,pid,content,user_id,likes,comments,createtime')->find();
        if ($pid && $this->config['showtype'] == 'reply') {
            $parentComment = Post::get($pid, 'userinfo');
            $parentComment['content'] = mb_substr($parentComment['content'], 0, 255);
            $comment['content'] = "<blockquote><a href='" . $parentComment['userinfo']['url'] . "' target='_blank'>@" . $parentComment['userinfo']['nickname'] . "</a> " . $parentComment['content'] . "</blockquote>" . $comment['content'];
        }
        if ($data['status'] == 'hidden') {
            $comment['content'] .= "<div class='ds-alert'>发表成功！评论需要后台审核后才会显示</div>";
        }
        $data = [
            'token'   => $this->request->token(),
            'comment' => $comment
        ];
        $this->success('评论发表成功', null, $data);
    }

    /**
     * 评论列表
     */
    public function comment()
    {
        $sort = $this->request->request('sort');
        $pid = (int)$this->request->request('pid');
        $level = (int)$this->request->request('level');
        if ($this->config['showtype'] == 'floor') {
            $level = $pid ? $level : 2;
            if ($level < 1 || $level > 5) {
                $this->error("参数不正确");
            }
        }
        $sortArr = [
            'new'   => 'id DESC',
            'early' => 'id ASC',
            'hot'   => 'comments DESC,id ASC',
        ];
        $orderby = isset($sortArr[$sort]) ? $sortArr[$sort] : $sortArr['new'];

        $datas = [];
        $totalCount = 0;
        $page = 1;
        $perPage = $this->config['pagesize'];
        $lastPage = 1;
        if ($this->article) {
            $commentList = Post::with(['userinfo'])
                ->where('site_id', $this->site->id)
                ->where('article_id', $this->article->id)
                ->where($this->config['showtype'] == 'reply' ? '' : ['pid' => $pid])
                ->where('status', 'normal')
                ->field('id,pid,content,user_id,likes,comments,createtime')
                ->order($orderby)
                ->paginate($pid ? $this->config['floorpagesize'] : $this->config['pagesize']);
            $commentData = $commentList->toArray();

            $commentList = [];
            $pids = [];
            foreach ($commentData['data'] as $index => $datum) {
                $datum['children'] = [];
                $pids[] = $datum['pid'];
                $commentList[$datum['id']] = $datum;
            }
            if ($commentList) {
                //如果是回贴方式
                if ($this->config['showtype'] == 'floor') {
                    Post::getChildrenCommentList($commentList, $this->site->id, $this->article->id, $level);
                } elseif ($this->config['showtype'] == 'reply') {
                    $pids = array_diff(array_filter(array_unique($pids)), array_keys($commentList));
                    if ($pids) {
                        $quoteCommentList = Post::with(['userinfo'])
                            ->where('id', 'in', $pids)
                            ->field('id,pid,content,user_id,likes,comments,createtime')
                            ->select();
                        $quoteCommentData = collection($quoteCommentList)->toArray();
                        $quoteCommentList = [];
                        foreach ($quoteCommentData as $index => &$datum) {
                            $quoteCommentList[$datum['id']] = $datum;
                        }
                        unset($datum);
                    }
                    foreach ($commentList as $index => &$item) {
                        if ($item['pid']) {
                            $parentComment = isset($commentList[$item['pid']]) ? $commentList[$item['pid']] : (isset($quoteCommentList[$item['pid']]) ? $quoteCommentList[$item['pid']] : '');
                            if ($parentComment) {
                                $parentComment['content'] = mb_substr($parentComment['content'], 0, 255);
                                $item['content'] = "<blockquote><a href='" . $parentComment['userinfo']['url'] . "' target='_blank'>@" . $parentComment['userinfo']['nickname'] . "</a> " . $parentComment['content'] . "</blockquote>" . $item['content'];
                            }
                        }
                    }
                    unset($item);
                }
            }
            $datas = array_values($commentList);
            $totalCount = $commentData['total'];
            $page = $commentData['current_page'];
            $perPage = $commentData['per_page'];
            $lastPage = $commentData['last_page'];
        }
        $data = [
            'datas'      => (array)$datas,
            "totalCount" => (int)$totalCount,
            "page"       => (int)$page,
            "perPage"    => (int)$perPage,
            "lastPage"   => (int)$lastPage
        ];
        $this->success('ok', null, $data);
    }

    /**
     * 登录和登录回调
     */
    public function login()
    {
        if ($this->auth->isLogin()) {
            $this->view->assign('host', $this->request->host());
            return $this->view->fetch();
        }
        $type = $this->request->request('type');
        if (!in_array($type, explode(',', $this->config['thirdlogin']))) {
            $this->error('暂未开放的登录方式');
        }

        if ($type == 'account') {
            $url = url("index/user/login") . '?url=' . urlencode($this->request->url(true));
        } else {
            $url = "/third/connect/{$type}" . '?url=' . urlencode($this->request->url(true));
        }
        $this->redirect($url);
    }

}
