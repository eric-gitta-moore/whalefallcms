<?php

namespace app\common\library;

use app\common\model\User;
use app\common\model\UserRule;
use fast\Random;
use fast\Tree;
use think\Config;
use think\Db;
use think\Hook;
use think\Request;
use think\Session;
use think\Validate;

class UserAuth
{
    protected static $instance = null;
    protected $_error = '';
    protected $_logined = false;
    protected $_user = null;
    protected $_token = '';
    //Token默认有效时长
    protected $keeptime = 2592000;
    protected $requestUri = '';
    protected $rules = [];
    //默认配置
    protected $config = [];
    protected $options = [];
    protected $allowFields = ['nickname', 'mobile', 'avatar', 'score', 'bio'];
    //权限菜单
    protected $menus = [];
    //面包屑导航
    protected $breadcrumb = [];

    public function __construct($options = [])
    {
        if ($config = Config::get('user')) {
            $this->options = array_merge($this->config, $config);
        }
        $this->options = array_merge($this->config, $options);
    }

    /**
     *
     * @param array $options 参数
     * @return Auth
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * 获取User模型
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * 兼容调用user模型的属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_user ? $this->_user->$name : null;
    }

    /**
     * 根据Token初始化
     *
     * @param string $token Token
     * @return boolean
     */
    public function init($token)
    {
        if ($this->_logined) {
            return true;
        }
        if ($this->_error)
            return false;
        $data = Token::get($token);
        if (!$data) {
            return false;
        }
        $user_id = intval($data['user_id']);
        if ($user_id > 0) {
            $user = User::get($user_id);
            if (!$user) {
                $this->setError('Account not exist');
                return false;
            }
            if ($user['status'] != 'normal') {
                $this->setError('Account is locked');
                return false;
            }
            $this->_user = $user;
            $this->_logined = true;
            $this->_token = $token;

            //初始化成功的事件
            Hook::listen("user_init_successed", $this->_user);

            return true;
        } else {
            $this->setError('You are not logged in');
            return false;
        }
    }

    /**
     * 注册用户
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email 邮箱
     * @param string $mobile 手机号
     * @param array $extend 扩展参数
     * @return boolean
     */
    public function register($username, $password, $email = '', $mobile = '', $extend = [])
    {
        // 检测用户名或邮箱、手机号是否存在
        if (User::getByUsername($username)) {
            $this->setError('Username already exist');
            return false;
        }
        if ($email && User::getByEmail($email)) {
            $this->setError('Email already exist');
            return false;
        }
        if ($mobile && User::getByMobile($mobile)) {
            $this->setError('Mobile already exist');
            return false;
        }

        $ip = request()->ip();
        $time = time();

        $data = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'mobile' => $mobile,
            'level' => 1,
            'score' => 0,
            'avatar' => '',
        ];
        $params = array_merge($data, [
            'nickname' => $username,
            'salt' => Random::alnum(),
            'jointime' => $time,
            'joinip' => $ip,
            'logintime' => $time,
            'loginip' => $ip,
            'prevtime' => $time,
            'status' => 'normal'
        ]);
        $params['password'] = $this->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);

        ////////////////同步到Ucenter////////////////
        if (defined('UC_STATUS') && UC_STATUS) {
            $uc = new \addons\ucenter\library\client\Client();
            $user_id = $uc->uc_user_register($username, $password, $email);
            // 如果小于0则说明发生错误
            if ($user_id <= 0) {
                $this->setError($user_id > -4 ? 'Username is incorrect' : 'Email is incorrect');
                return false;
            } else {
                $params['id'] = $user_id;
            }
        }

        //账号注册时需要开启事务,避免出现垃圾数据
        Db::startTrans();
        try {
            $user = User::create($params, true);
            $this->_user = User::get($user->id);

            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user->id, $this->keeptime);

            //注册成功的事件
            Hook::listen("user_register_successed", $this->_user, $data);

            Db::commit();
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            Db::rollback();
            return false;
        }
        return true;
    }

    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt 密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 用户登录
     *
     * @param string $account 账号,用户名、邮箱、手机号
     * @param string $password 密码
     * @return boolean
     */
    public function login($account, $password)
    {
        $field = Validate::is($account, 'email') ? 'email' : (Validate::regex($account, '/^1\d{10}$/') ? 'mobile' : 'username');
        $user = User::get([$field => $account]);
        if (!$user) {
            $this->setError('Account is incorrect');
            return false;
        }

        if ($user->status != 'normal') {
            $this->setError('Account is locked');
            return false;
        }
        if ($user->password != $this->getEncryptPassword($password, $user->salt)) {
            $this->setError('Password is incorrect');
            return false;
        }

        //直接登录会员
        $this->direct($user->id);

        return true;
    }

    /**
     * 直接登录账号
     * @param int $user_id
     * @return boolean
     */
    public function direct($user_id)
    {
        $user = User::get($user_id);
        if ($user) {
            ////////////////同步到Ucenter////////////////
            if (defined('UC_STATUS') && UC_STATUS) {
                $uc = new \addons\ucenter\library\client\Client();
                $re = $uc->uc_user_login($this->user->id, $this->user->password . '#split#' . $this->user->salt, 3);
                // 如果小于0则说明发生错误
                if ($re <= 0) {
                    $this->setError('Username or password is incorrect');
                    return false;
                }
            }
            Db::startTrans();
            try {

                $ip = request()->ip();
                $time = time();

                //判断连续登录和最大连续登录
                if ($user->logintime < \fast\Date::unixtime('day')) {
                    $user->successions = $user->logintime < \fast\Date::unixtime('day', -1) ? 1 : $user->successions + 1;
                    $user->maxsuccessions = max($user->successions, $user->maxsuccessions);
                }

                $user->prevtime = $user->logintime;
                //记录本次登录的IP和时间
                $user->loginip = $ip;
                $user->logintime = $time;

                $user->save();

                //查询用户信息之后, 查询用户的积分等级
                $levelId = $user['level'];
                $levelName = Db::name("user_level")->where("level_id", $levelId)->column("level_name");
                $user['level_name'] = $levelName;

                $this->_user = $user;

                $this->_token = Random::uuid();
                Token::set($this->_token, $user->id, $this->keeptime);

                $this->_logined = true;

                //登录成功的事件
                Hook::listen("user_login_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 注销
     *
     * @return boolean
     */
    public function logout()
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //设置登录标识
        $this->_logined = false;
        //删除Token
        Token::delete($this->_token);
        //注销成功的事件
        Hook::listen("user_logout_successed", $this->_user);
        return true;
    }

    /**
     * 修改密码
     * @param string $newpassword 新密码
     * @param string $oldpassword 旧密码
     * @param bool $ignoreoldpassword 忽略旧密码
     * @return boolean
     */
    public function changepwd($newpassword, $oldpassword = '', $ignoreoldpassword = false)
    {
        if (!$this->_logined) {
            $this->setError('You are not logged in');
            return false;
        }
        //判断旧密码是否正确
        if ($this->_user->password == $this->getEncryptPassword($oldpassword, $this->_user->salt) || $ignoreoldpassword) {
            Db::startTrans();
            try {
                $salt = Random::alnum();
                $newpassword = $this->getEncryptPassword($newpassword, $salt);
                $this->_user->save(['password' => $newpassword, 'salt' => $salt]);

                Token::delete($this->_token);
                //修改密码成功的事件
                Hook::listen("user_changepwd_successed", $this->_user);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->setError($e->getMessage());
                return false;
            }
            return true;
        } else {
            $this->setError('Password is incorrect');
            return false;
        }
    }

    /**
     * 检测是否是否有对应权限
     * @param string $path 控制器/方法
     * @param string $module 模块 默认为当前模块
     * @return boolean
     */
    public function check($path = null, $module = null)
    {
        if (!$this->_logined) return false;

        $ruleList = $this->getRuleList();
        $rules = [];
        foreach ($ruleList as $k => $v) {
            $rules[] = $v['name'];
        }
        $url = ($module ? $module : request()->module()) . '/' . (is_null($path) ? $this->getRequestUri() : $path);
        $url = is_null($path) ? $this->getRequestUri() : $path;
        $url = strtolower(str_replace('.', '/', $url));
        return in_array($url, $rules) ? true : false;
    }

    /**
     * 获取会员组别规则列表
     * @return array
     */
    public function getRuleList()
    {
        if ($this->rules) return $this->rules;
        if (!$this->_user) return [];
        $group = $this->_user->group;
        if (!$group) return [];
        $rules = explode(',', $group->rules);
        $this->rules = UserRule::where('status', 'normal')->where('id', 'in', $rules)->field('id,pid,name,title,ismenu')->select();
        return $this->rules;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * 设置当前请求的URI
     * @param string $uri
     */
    public function setRequestUri($uri)
    {
        $this->requestUri = $uri;
    }

    /**
     * 判断是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->_logined) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前Token
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * 获取会员基本信息
     */
    public function getUserinfo()
    {
        $data = $this->_user->toArray();
        $allowFields = $this->getAllowFields();
        $userinfo = array_intersect_key($data, array_flip($allowFields));
        $userinfo = array_merge($userinfo, Token::get($this->_token));
        return $userinfo;
    }

    /**
     * 获取允许输出的字段
     * @return array
     */
    public function getAllowFields()
    {
        return $this->allowFields;
    }

    /**
     * 重新设置允许输出的字段
     * @param array $fields
     */
    public function setAllowFields($fields)
    {
        $this->allowFields = $fields;
    }

    /**
     * 删除一个指定会员
     * @param int $user_id 会员ID
     * @return boolean
     */
    public function delete($user_id)
    {
        $user = User::get($user_id);
        if (!$user) {
            return false;
        }

        ////////////////同步到Ucenter////////////////
        if (defined('UC_STATUS') && UC_STATUS) {
            $uc = new \addons\ucenter\library\client\Client();
            $re = $uc->uc_user_delete($user['id']);
            // 如果小于0则说明发生错误
            if ($re <= 0) {
                $this->setError('Account is locked');
                return false;
            }
        }

        Db::startTrans();
        try {
            // 删除会员
            User::destroy($user_id);
            // 删除会员指定的所有Token
            Token::clear($user_id);

            Hook::listen("user_delete_successed", $user);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 检测当前控制器和方法是否匹配传递的数组
     *
     * @param array $arr 需要验证权限的数组
     * @return boolean
     */
    public function match($arr = [])
    {
        $request = Request::instance();
        $arr = is_array($arr) ? $arr : explode(',', $arr);
        if (!$arr) {
            return false;
        }
        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /**
     * 设置会话有效时间
     * @param int $keeptime 默认为永久
     */
    public function keeptime($keeptime = 0)
    {
        $this->keeptime = $keeptime;
    }

    /**
     * 渲染用户数据
     * @param array $datalist 二维数组
     * @param mixed $fields 加载的字段列表
     * @param string $fieldkey 渲染的字段
     * @param string $renderkey 结果字段
     * @return array
     */
    public function render(&$datalist, $fields = [], $fieldkey = 'user_id', $renderkey = 'userinfo')
    {
        $fields = !$fields ? ['id', 'nickname', 'level', 'avatar'] : (is_array($fields) ? $fields : explode(',', $fields));
        $ids = [];
        foreach ($datalist as $k => $v) {
            if (!isset($v[$fieldkey]))
                continue;
            $ids[] = $v[$fieldkey];
        }
        $list = [];
        if ($ids) {
            if (!in_array('id', $fields)) {
                $fields[] = 'id';
            }
            $ids = array_unique($ids);
            $selectlist = User::where('id', 'in', $ids)->column($fields);
            foreach ($selectlist as $k => $v) {
                $list[$v['id']] = $v;
            }
        }
        foreach ($datalist as $k => &$v) {
            $v[$renderkey] = isset($list[$v[$fieldkey]]) ? $list[$v[$fieldkey]] : null;
        }
        unset($v);
        return $datalist;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error ? __($this->_error) : '';
    }

    /**
     * 设置错误信息
     *
     * @param $error 错误信息
     * @return Auth
     */
    public function setError($error)
    {
        $this->_error = $error;
        return $this;
    }

    /**
     * 获得面包屑导航
     * @param string $path
     * @return array
     */
    public function getBreadCrumb($path = '')
    {
        if ($this->breadcrumb || !$path) return $this->breadcrumb;
        $path_rule_id = 0;
        if (empty($this->rules)) {
            $this->rules = $this->getRuleList();
        }
        $path = str_replace(".", "/", $path);
        foreach ($this->rules as $rule) {
            $path_rule_id = $rule['name'] == $path ? $rule['id'] : $path_rule_id;
        }
        if ($path_rule_id) {
            $this->breadcrumb = Tree::instance()->init($this->rules)->getParents($path_rule_id, true);
            foreach ($this->breadcrumb as $k => &$v) {
                $v['url'] = url($v['name']);
                $v['title'] = __($v['title']);
            }
        }
        return $this->breadcrumb;
    }

    /**
     * 获取左侧菜单栏
     *
     * @param array $params URL对应的badge数据
     * @return string
     */
    public function getSidebar($params = [], $fixedPage = 'dashboard')
    {
        $colorArr = ['red', 'green', 'yellow', 'blue', 'teal', 'orange', 'purple'];
        $colorNums = count($colorArr);
        $badgeList = [];
        $module = request()->module();
        // 生成菜单的badge
        foreach ($params as $k => $v) {
            $url = $k;
            if (is_array($v)) {
                $nums = isset($v[0]) ? $v[0] : 0;
                $color = isset($v[1]) ? $v[1] : $colorArr[(is_numeric($nums) ? $nums : strlen($nums)) % $colorNums];
                $class = isset($v[2]) ? $v[2] : 'label';
            } else {
                $nums = $v;
                $color = $colorArr[(is_numeric($nums) ? $nums : strlen($nums)) % $colorNums];
                $class = 'label';
            }
            //必须nums大于0才显示
            if ($nums) {
                $badgeList[$url] = '<small class="' . $class . ' pull-right bg-' . $color . '">' . $nums . '</small>';
            }
        }
        // 读取用户当前拥有的权限节点
        $userRule = $this->getRuleList();
        $selected = $referer = [];
        $refererUrl = Session::get('referer');
        $pinyin = new \Overtrue\Pinyin\Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        // 必须将结果集转换为数组
        //$ruleList = collection(UserRule::where('status', 'normal')->where('ismenu', 1)->order('weigh', 'desc')->select())->toArray();//->cache("__usermenu__")
        $ruleList = $this->getMenuList();
        foreach ($ruleList as $k => &$v) {
            /*if (!in_array($v['name'], $userRule)) {
             unset($ruleList[$k]);
             continue;
             }*/
            $v['icon'] = $v['icon'] . ' fa-fw';
            $v['url'] = '/' . $module . '/' . $v['name'];
            $v['badge'] = isset($badgeList[$v['name']]) ? $badgeList[$v['name']] : '';
            $v['py'] = $pinyin->abbr($v['title'], '');
            $v['pinyin'] = $pinyin->permalink($v['title'], '');
            $v['title'] = __($v['title']);
            //$select_id = $v['name'] == $fixedPage ? $v['id'] : $select_id;
            $selected = $v['name'] == $fixedPage ? $v : $selected;
            $referer = url($v['url']) == $refererUrl ? $v : $referer;
        }
        if ($selected == $referer) {
            $referer = [];
        }
        $selected && $selected['url'] = url($selected['url']);
        $referer && $referer['url'] = url($referer['url']);

        $select_id = $selected ? $selected['id'] : 0;
        $menu = $nav = '';
        //是否启用多级菜单导航
        if (Config::get('fastadmin.multiplenav')) {
            $topList = [];
            foreach ($ruleList as $index => $item) {
                if (!$item['pid']) {
                    $topList[] = $item;
                }
            }
            $selectParentIds = [];
            $tree = Tree::instance();
            $tree->init($ruleList);
            if ($select_id) {
                $selectParentIds = $tree->getParentsIds($select_id, true);
            }
            foreach ($topList as $index => $item) {
                $childList = Tree::instance()->getTreeMenu($item['id'], '<li class="@class" pid="@pid"><a href="@url@addtabs" addtabs="@id" url="@url" py="@py" pinyin="@pinyin"><i class="@icon"></i> <span>@title</span> <span class="pull-right-container">@caret @badge</span></a> @childlist</li>', $select_id, '', 'ul', 'class="treeview-menu"');
                $current = in_array($item['id'], $selectParentIds);
                $url = $childList ? 'javascript:;' : url($item['url']);
                $addtabs = $childList || !$url ? "" : (stripos($url, "?") !== false ? "&" : "?") . "ref=addtabs";
                $childList = str_replace('" pid="' . $item['id'] . '"', ' treeview ' . ($current ? '' : 'hidden') . '" pid="' . $item['id'] . '"', $childList);
                $nav .= '<li class="' . ($current ? 'active' : '') . '"><a href="' . $url . $addtabs . '" addtabs="' . $item['id'] . '" url="' . $url . '"><i class="' . $item['icon'] . '"></i> <span>' . $item['title'] . '</span> <span class="pull-right-container"> </span></a> </li>';
                $menu .= $childList;
            }
        } else {
            // 构造菜单数据
            Tree::instance()->init($ruleList);
            $menu = Tree::instance()->getTreeMenu(0, '<li class="@class"><a href="@url@addtabs" addtabs="@id" url="@url" py="@py" pinyin="@pinyin"><i class="@icon"></i> <span>@title</span> <span class="pull-right-container">@caret @badge</span></a> @childlist</li>', $select_id, '', 'ul', 'class="treeview-menu"');
            if ($selected) {
                $nav .= '<li role="presentation" id="tab_' . $selected['id'] . '" class="' . ($referer ? '' : 'active') . '"><a href="#con_' . $selected['id'] . '" node-id="' . $selected['id'] . '" aria-controls="' . $selected['id'] . '" role="tab" data-toggle="tab"><i class="' . $selected['icon'] . ' fa-fw"></i> <span>' . $selected['title'] . '</span> </a></li>';
            }
            if ($referer) {
                $nav .= '<li role="presentation" id="tab_' . $referer['id'] . '" class="active"><a href="#con_' . $referer['id'] . '" node-id="' . $referer['id'] . '" aria-controls="' . $referer['id'] . '" role="tab" data-toggle="tab"><i class="' . $referer['icon'] . ' fa-fw"></i> <span>' . $referer['title'] . '</span> </a> <i class="close-tab fa fa-remove"></i></li>';
            }
        }
        return [$menu, $nav, $selected, $referer];
        // 构造菜单数据
        //$usertree = new Tree();
        //$usertree->init($ruleList);
        //$usermenu = $usertree->getTreeMenu(0, '<li class="@class"><a href="@url@addtabs" addtabs="@id" url="@url" py="@py" pinyin="@pinyin"><i class="@icon"></i> <span>@title</span> <span class="pull-right-container">@caret @badge</span></a> @childlist</li>'."\n", $select_id, '', 'ul', 'class="treeview-menu"');
        //return $usermenu;
    }

    /**
     * 获取会员组别规则菜单
     * @return array
     */
    public function getMenuList()
    {
        if ($this->menus) return $this->menus;
        $group = $this->_user->group;
        if (!$group) return [];
        $rules = explode(',', $group->rules);
        $this->menus = collection(UserRule::where('status', 'normal')->where('id', 'in', $rules)->where('ismenu', 1)
            ->order('weigh', 'desc')->select())->toArray();//->field('id,pid,name,title,ismenu,status,icon')
        return $this->menus;
    }

    /**
     * 获取等级
     * @return array
     */
    public function getLevel()
    {
        if ($this->_logined) {//登录才能获取等级
            $rank = $this->_user->level;
            if (!$rank) {
                return false;
            }
            $lv = \think\Db::name('user_level')->where('level_id', $rank)->find();//->column('level_id,level_name,level_img');
            $this->addAllowFields(['level_name', 'level_img']);
            return $this->set(['level_name' => $lv['level_name'], 'level_img' => $lv['level_img']]);
        }
    }

    /**
     * 追加设置允许输出的字段
     * @param array $fields
     */
    public function addAllowFields($fields)
    {
        $this->allowFields = array_merge(
            $this->allowFields, array_change_key_case($fields)
        );
    }

    /**
     * 设置user扩展属性， name 为数组则为批量设置
     * @access public
     * @param  string|array $name 配置参数名（支持二级配置 . 号分割）
     * @param  mixed $value 配置值
     * @return mixed 具有扩展属性的user对象
     */
    public function set($name, $value = null)
    {

        if (!isset($this->_user)) $this->_user = [];

        // 字符串则表示单个配置设置
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                $this->_user[strtolower($name)] = $value;
            } else {
                // 二维数组
                $name = explode('.', $name, 2);
                $this->_user[strtolower($name[0])][$name[1]] = $value;
            }
            return $value;
        }

        // 数组则表示批量设置
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                if (!strpos($k, '.')) {
                    $this->_user[strtolower($k)] = $v;
                } else {
                    // 二维数组
                    $k = explode('.', $k, 2);
                    $this->_user[strtolower($k[0])][$k[1]] = $v;
                }
            }
            return $this->_user;
        }

        // 为空直接返回已有配置
        return $this->_user;
    }

}
