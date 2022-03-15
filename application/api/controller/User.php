<?php
/**
 * 云凌鲸落小说漫画聚合分销CMS系统
 * @Author Curtis - 云凌工作室
 * @Website http://www.whalefallcms.com
 * @Datetime 2020/4/8 下午 05:07
 */

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\cartoon\Cartoon;
use app\common\model\user\Collection;
use app\service\CollectionService;
use app\service\UserService;
use fast\Random;
use think\Db;
use think\Exception;
use think\Validate;
use function fast\e;

/**
 * 会员接口
 */
class User extends Api
{
    protected $noNeedLogin = [
        'login',
        'mobilelogin',
        'register',
        'resetpwd',
        'changeemail',
        'changemobile',
        'third'
    ];

    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }

    /**
     * 会员登录
     *
     * @param string $account 账号
     * @param string $password 密码
     */
    public function login()
    {
        $account = $this->request->request('account');
        $password = $this->request->request('password');
        if (!$account || !$password) {
            $this->error(__('Invalid parameters'));
        }
        $ret = $this->auth->login($account, $password);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 手机验证码登录
     *
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function mobilelogin()
    {
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (!Sms::check($mobile, $captcha, 'mobilelogin')) {
            $this->error(__('Captcha is incorrect'));
        }
        $user = \app\common\model\User::getByMobile($mobile);
        if ($user) {
            if ($user->status != 'normal') {
                $this->error(__('Account is locked'));
            }
            //如果已经有账号则直接登录
            $ret = $this->auth->direct($user->id);
        } else {
            $ret = $this->auth->register($mobile, Random::alnum(), '', $mobile, []);
        }
        if ($ret) {
            Sms::flush($mobile, 'mobilelogin');
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Logged in successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注册会员
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param string $code 验证码
     */
    public function register()
    {
        $username = $this->request->request('username');
        $password = $this->request->request('password');
        $email = $this->request->request('email');
        $mobile = $this->request->request('mobile');
        $code = $this->request->request('code');
        if (!$username || !$password) {
            $this->error(__('Invalid parameters'));
        }
        if ($email && !Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($mobile && !Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        $ret = Sms::check($mobile, $code, 'register');
        if (!$ret) {
            $this->error(__('Captcha is incorrect'));
        }
        $ret = $this->auth->register($username, $password, $email, $mobile, []);
        if ($ret) {
            $data = ['userinfo' => $this->auth->getUserinfo()];
            $this->success(__('Sign up successful'), $data);
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $this->success(__('Logout successful'));
    }

    /**
     * 修改会员个人信息
     *
     * @param string $avatar 头像地址
     * @param string $username 用户名
     * @param string $nickname 昵称
     * @param string $bio 个人简介
     */
    public function profile()
    {
        $user = $this->auth->getUser();
        $username = $this->request->request('username');
        $nickname = $this->request->request('nickname');
        $bio = $this->request->request('bio');
        $avatar = $this->request->request('avatar', '', 'trim,strip_tags,htmlspecialchars');
        if ($username) {
            $exists = \app\common\model\User::where('username', $username)->where('id', '<>', $this->auth->id)->find();
            if ($exists) {
                $this->error(__('Username already exists'));
            }
            $user->username = $username;
        }
        $user->nickname = $nickname;
        $user->bio = $bio;
        $user->avatar = $avatar;
        $user->save();
        $this->success();
    }

    /**
     * 修改邮箱
     *
     * @param string $email 邮箱
     * @param string $captcha 验证码
     */
    public function changeemail()
    {
        $user = $this->auth->getUser();
        $email = $this->request->post('email');
        $captcha = $this->request->request('captcha');
        if (!$email || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if (\app\common\model\User::where('email', $email)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Email already exists'));
        }
        $result = Ems::check($email, $captcha, 'changeemail');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->email = 1;
        $user->verification = $verification;
        $user->email = $email;
        $user->save();

        Ems::flush($email, 'changeemail');
        $this->success();
    }

    /**
     * 修改手机号
     *
     * @param string $mobile 手机号
     * @param string $captcha 验证码
     */
    public function changemobile()
    {
        $user = $this->auth->getUser();
        $mobile = $this->request->request('mobile');
        $captcha = $this->request->request('captcha');
        if (!$mobile || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if (!Validate::regex($mobile, "^1\d{10}$")) {
            $this->error(__('Mobile is incorrect'));
        }
        if (\app\common\model\User::where('mobile', $mobile)->where('id', '<>', $user->id)->find()) {
            $this->error(__('Mobile already exists'));
        }
        $result = Sms::check($mobile, $captcha, 'changemobile');
        if (!$result) {
            $this->error(__('Captcha is incorrect'));
        }
        $verification = $user->verification;
        $verification->mobile = 1;
        $user->verification = $verification;
        $user->mobile = $mobile;
        $user->save();

        Sms::flush($mobile, 'changemobile');
        $this->success();
    }

    /**
     * 第三方登录
     *
     * @param string $platform 平台名称
     * @param string $code Code码
     */
    public function third()
    {
        $url = url('user/index');
        $platform = $this->request->request("platform");
        $code = $this->request->request("code");
        $config = get_addon_config('third');
        if (!$config || !isset($config[$platform])) {
            $this->error(__('Invalid parameters'));
        }
        $app = new \addons\third\library\Application($config);
        //通过code换access_token和绑定会员
        $result = $app->{$platform}->getUserInfo(['code' => $code]);
        if ($result) {
            $loginret = \addons\third\library\Service::connect($platform, $result);
            if ($loginret) {
                $data = [
                    'userinfo' => $this->auth->getUserinfo(),
                    'thirdinfo' => $result
                ];
                $this->success(__('Logged in successful'), $data);
            }
        }
        $this->error(__('Operation failed'), $url);
    }

    /**
     * 重置密码
     *
     * @param string $mobile 手机号
     * @param string $newpassword 新密码
     * @param string $captcha 验证码
     */
    public function resetpwd()
    {
        $type = $this->request->request("type");
        $mobile = $this->request->request("mobile");
        $email = $this->request->request("email");
        $newpassword = $this->request->request("newpassword");
        $captcha = $this->request->request("captcha");
        if (!$newpassword || !$captcha) {
            $this->error(__('Invalid parameters'));
        }
        if ($type == 'mobile') {
            if (!Validate::regex($mobile, "^1\d{10}$")) {
                $this->error(__('Mobile is incorrect'));
            }
            $user = \app\common\model\User::getByMobile($mobile);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Sms::check($mobile, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Sms::flush($mobile, 'resetpwd');
        } else {
            if (!Validate::is($email, "email")) {
                $this->error(__('Email is incorrect'));
            }
            $user = \app\common\model\User::getByEmail($email);
            if (!$user) {
                $this->error(__('User not found'));
            }
            $ret = Ems::check($email, $captcha, 'resetpwd');
            if (!$ret) {
                $this->error(__('Captcha is incorrect'));
            }
            Ems::flush($email, 'resetpwd');
        }
        //模拟一次登录
        $this->auth->direct($user->id);
        $ret = $this->auth->changepwd($newpassword, '', true);
        if ($ret) {
            $this->success(__('Reset password successful'));
        } else {
            $this->error($this->auth->getError());
        }
    }

    /**
     * 添加收藏
     * @param int $book_id
     * @param string $type
     * @throws Exception
     */
    public function addCollection($book_id, $type = 'cartoon')
    {
        $type = strtolower($type);
        $user = $this->auth->getUser();
        //关联查询不会自动添加deletetime判断
        $cnt = $user->hasWhere('collection', [
            'bookid' => $book_id,
            'status' => $type,
            'deletetime' => null
        ])->count();

        if ($cnt > 0) {
            $this->error('已经收藏了这本书');
        }

        //新增收藏
        Db::startTrans();
        try {
            $user->collection()->save([
                'bookid' => $book_id,
                'status' => $type,
                'weigh' => $cnt
            ]);

            $model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
            $collect = $model::where([
                'id' => $book_id
            ]);
            if (config('site.put_off_update_collection')) {
                $collect->setInc('collectnum', 1, 60);
            } else {
                $collect->setInc('collectnum');
            }
            Db::commit();

            $this->success('收藏成功');
        } catch (Exception $exception) {
            Db::rollback();
            $this->error('请求失败:' . $exception->getMessage());
        }
    }

    /**
     * 删除单个收藏
     * @param $book_id
     * @param string $type
     * @throws Exception
     */
    public function cancelCollection($book_id, $type = 'cartoon')
    {
        $type = strtolower($type);
        $user = $this->auth->getUser();
        //关联查询不会自动添加deletetime判断
        $cnt = $user->hasWhere('collection', [
            'bookid' => $book_id,
            'status' => $type,
            'deletetime' => null
        ])->count();

        if ($cnt == 0) {
            $this->error('还没有收藏这本书哦');
        }

        //新增收藏
        Db::startTrans();
        try {
            $user->collection()->where([
                'bookid' => $book_id,
                'status' => $type,
//                'user_id' => $user->id,
            ])->delete();


            $model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
            $collect = $model::where([
                'id' => $book_id
            ]);
            if (config('site.put_off_update_collection')) {
                $collect->setDec('collectnum', 1, 60);
            } else {
                $collect->setDec('collectnum');
            }

            Db::commit();
            $this->success('取消收藏成功');
        } catch (Exception $exception) {
            Db::rollback();
            $this->error('请求失败:' . $exception->getMessage());
        }

    }

    /**
     * 批量删除收藏
     */
    public function cancelCollectionAll()
    {
        if (!input('data'))
            $this->error('参数错误');

        Db::startTrans();
        try {
            $user = $this->auth->getUser();

            if (input('data') == 'all') {
                $result = $user->collection()->field('bookid,status')->select();
                $delete_array = [];
                foreach ($result as $item) {
                    $delete_array[$item['status']][$item['bookid']] = $item['bookid'];
                }

                /*
                 * Array
                    (
                        [cartoon] => Array
                            (
                                [2] => 2
                            )

                    )
                 */

                foreach ($delete_array as $type => $value) {

                    //书本收藏数-1
                    $model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
                    $collect = $model::where([
                        'id' => ['in', $value]
                    ]);
                    if (config('site.put_off_update_collection')) {
                        $collect->setDec('collectnum', 1, 60);
                    } else {
                        $collect->setDec('collectnum');
                    }

                }
                $user->collection()->where(['user_id' => $user->id])->delete();
            } else {

                $data = parse_key_value(input('data'));
                $parse_data = [];
                foreach ($data as $k => $v) {
                    $parse_data[$v][] = $k;

                }

                /*
                 * Array
                    (
                        [cartoon] => Array
                            (
                                [0] => 2
                            )

                    )
                 */

                foreach ($parse_data as $k => $datum) {
                    if (empty($datum))
                        continue;

                    //删除用户收藏
                    $user->collection()->where([
                        'status' => $k,
                        'bookid' => ['in', $datum]
                    ])->delete();

                    //书本收藏数-1
                    $model = '\app\common\model\\' . $k . '\\' . ucfirst($k);
                    $collect = $model::where([
                        'id' => ['in', $datum]
                    ]);
                    if (config('site.put_off_update_collection')) {
                        $collect->setDec('collectnum', 1, 60);
                    } else {
                        $collect->setDec('collectnum');
                    }

                }


            }

            Db::commit();
            $this->success('删除成功');
        } catch (Exception $exception) {
            Db::rollback();
            $this->error('处理出错:' . $exception->getMessage());
        }
//        print_r($parse_data);

    }

    /**
     * 点赞
     * @param int $id
     * @param string $type
     */
    public function addLike($id, $type = 'cartoon')
    {
        $type = check_type($type);
        if (!$type)
            $this->error('错误');
        $chapter_id = $id;
        $chapter_model = '\app\common\model\\' . $type . '\\Chapter';
        $book_model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
//        $chapter_info = $chapter_model::where([
//            'id' => $chapter_id
//        ])->with($type)->find();
//        var_dump($chapter_id,$id);
//        echo $chapter_info;exit();

        $user = $this->auth->getUser();
        if ($type = check_type($type)) {
//            $book_id = $chapter_info[$type]['id'];
            $book_id = $id;

            $relation = $type . 'Likes';
            $book_primary_key = $type . '_' . $type . '_id';
            $cnt = $user->$relation()->where([
                $book_primary_key => $book_id,
            ])->count();

            if ($cnt > 0) {
                $this->error('已经赞过了');
            } else {
                Db::startTrans();
                try {
                    $user->$relation()->insert([
                        $book_primary_key => $book_id,
                        'user_id' => $user->id,
                        'createtime' => time(),
                    ]);
                    $likes = $book_model::where(['id' => $book_id]);
                    if (config('site.put_off_update_like_number')) {
                        $likes->setInc('likenum', 1, 60);
                    } else {
                        $likes->setInc('likenum');
                    }
                    Db::commit();
                    $this->success('点赞成功');
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error('点赞失败:' . $e->getMessage());
                }

            }
        } else {
            $this->error('参数错误', ['type' => $type]);
        }
    }

    /**
     * 取消点赞
     * @param int $id
     * @param string $type
     */
    public function cancelLike($id, $type = 'cartoon')
    {
        $type = check_type($type);
        if (!$type)
            $this->error('错误');
        $chapter_id = $id;
        $chapter_model = '\app\common\model\\' . $type . '\\Chapter';
        $book_model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
//        $chapter_info = $chapter_model::where(['id' => $chapter_id])->with($type)->find();

        $user = $this->auth->getUser();
        if ($type = check_type($type)) {
//            $book_id = $chapter_info[$type]['id'];
            $book_id = $id;
            $relation = $type . 'Likes';
            $book_primary_key = $type . '_' . $type . '_id';
            $cnt = $user->$relation()->where([
                $book_primary_key => $book_id,
            ])->count();

            if ($cnt == 0) {
                $this->error('您还没有赞过');
            } else {
                Db::startTrans();
                $likes_model = 'app\common\model\\' . $type . '\Likes';
                try {
                    $r = $likes_model::where([
                        $book_primary_key => $book_id,
                        'user_id' => $user->id,
                    ])->delete();
                    if (!$r)
                        throw new Exception('删除失败:' . $r);
//                    $book_model::where(['id' => $book_id])->setDec('likenum',1,60);
                    $likes = $book_model::where(['id' => $book_id]);
                    if (config('site.put_off_update_like_number')) {
                        $likes->setDec('likenum', 1, 60);
                    } else {
                        $likes->setDec('likenum');
                    }
                    Db::commit();
                    $this->success('取消赞成功');
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error('取消赞失败:' . $e->getMessage());
                }

            }
        } else {
            $this->error('参数错误', ['type' => $type]);
        }
    }

    /**
     * 开启/关闭自动付费
     */
    public function switchAutoPay()
    {
        $status = input('status/d');
        if (!in_array($status, [1, 0])) {
            $this->error('参数错误');
        }

        session('auto_pay', $status);

        $this->success('设置成功');
    }

    /**
     * 购买章节
     * @param int $id
     * @param string $type
     * @throws \think\exception\DbException
     */
    public function buyChapter($id = 0, $type = 'cartoon')
    {
        if (!empty($id))
            $id = input('id/d');
        if (!empty($type))
            $type = input('type', 'cartoon');
        $type = check_type($type);
        if (!$type || empty($id))
            $this->error('参数错误');

        $chapter_model = '\app\common\model\\' . $type . '\Chapter';
        $chapter = $chapter_model::get(['id', $id]);
        if (is_null($chapter)) {
            $this->error('没有该章节');
        }

        $user = $this->auth->getUser();

        if ($user->score < $chapter['money']) {
            $this->error(config('site.score_name') . __('不足'), ['need' => $chapter['money'], 'balance' => $user->score]);
        }

        $book_model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
        $book_update_buy_num = $book_model::get($chapter[$type . '_' . $type . '_id']);
        if (is_null($book_update_buy_num))
            $this->error('没有这本书');

        if (config('site.put_off_update_buy_number')) {
            $book_update_buy_num->setInc('chargenum', 1, 60);
        } else {
            $book_update_buy_num->setInc('chargenum');
        }
        \app\common\model\User::score($chapter['money'], $user->id, __('购买') . __('漫画') . __('章节') . ',id:' . $chapter['id']);
        $r = $user->buyLog()->save([
            'bookid' => $chapter[$type . '_' . $type . '_id'],
            'chapterid' => $chapter['id'],
            'status' => $type
        ]);
        if ($r) {
            $this->success('购买成功', [
                'chapter_id' => $chapter['id'],
                'cost_score' => $chapter['money'],
            ]);
        } else {
            $this->error('购买失败');
        }
    }

    public function collection($type = 0, $page = 1, $size = 10)
    {
        $user = $this->auth->getUser();
//        $collection = $user -> collection() -> paginate($size,false,['page' => $page]);
        $collection = CollectionService::getUserCollection($user->id, $type, true, true, true, $size, ['page' => $page]);
        $this->success('获取成功', $collection);
    }

    public function consumption($page = 1, $size = 10)
    {
        if ($size <= 0)
            $this->error('页面大小错误');

        $user = $this->auth->getUser();
        $page = 1;
        $size = 10;
        $total = 0;
        $cartoon_logs = $user->buyLog()
            ->where('status', 'cartoon')
            ->order('createtime desc')
            ->paginate($size, false, ['page' => $page]);
        $novel_logs = $user->buyLog()
            ->where('status', 'novel')
            ->order('createtime desc')
            ->paginate($size, false, ['page' => $page]);


        //分批查询漫画和小说购买记录
        $cartoon_chapter_info_result = [];
        if (!is_null($cartoon_logs)) {
            $total += $cartoon_logs -> total();
            $cartoon_chapter_info = \app\common\model\cartoon\Chapter::where([
                'id' => ['in', array_column($cartoon_logs->toArray()['data'], 'chapterid')],
            ])
                ->with(['book' => function ($query) {
                    $query->field('name,id');
                }])
                ->select();
            foreach ($cartoon_chapter_info as $item) {
                $arr['chapter_id'] = $item['id'];
                $arr['chapter_name'] = $item['name'];
                $arr['book_id'] = $item['book']['id'];
                $arr['book_name'] = $item['book']['name'];
                $arr['type'] = 'cartoon';
                $cartoon_chapter_info_result[] = $arr;
            }
        }
        $novel_chapter_info_result = [];
        if (!is_null($novel_logs)) {
            $total += $novel_logs -> total();
            $novel_chapter_info = \app\common\model\novel\Chapter::where([
                'id' => ['in', array_column($novel_logs->toArray()['data'], 'chapterid')],
            ])
                ->with(['book' => function ($query) {
                    $query->field('name,id');
                }])
                ->select();
            foreach ($novel_chapter_info as $item) {
                $arr['chapter_id'] = $item['id'];
                $arr['chapter_name'] = $item['name'];
                $arr['book_id'] = $item['book']['id'];
                $arr['book_name'] = $item['book']['name'];
                $arr['type'] = 'novel';
                $novel_chapter_info_result[] = $arr;
            }
        }

        //重组
        $cartoon_logs_re = [];
        foreach ($cartoon_logs as $cartoon_log) {
            $cartoon_logs_re[$cartoon_log['bookid'] . '_' . $cartoon_log['chapterid']] = $cartoon_log['createtime'];
        }
        $novel_logs_re = [];
        foreach ($novel_logs as $cartoon_log) {
            $novel_logs_re[$cartoon_log['bookid'] . '_' . $cartoon_log['chapterid']] = $cartoon_log['createtime'];
        }
        $result_pre = array_merge($cartoon_chapter_info_result, $novel_chapter_info_result);
        foreach ($result_pre as &$item) {
            $var = $item['type'] . '_logs_re';
            $var_name = $item['book_id'] . '_' . $item['chapter_id'];
            $item['create_time'] = $$var[$var_name];
            $item['create_time_text'] = datetime($$var[$var_name]);
        }

        array_multisort($result_pre, SORT_DESC, array_column($result_pre, 'create_time'));

        $result = [];
        $result['data'] = $result_pre;

        $result['total'] = $total;
        $result['last_page'] = ceil($total/$size);
        $result['per_page'] = $size;

        $this->success('获取成功', $result);

    }
}
