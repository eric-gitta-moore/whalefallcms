<?php

namespace app\admin\controller\development\auth;

use app\admin\model\AuthRule;
use app\common\controller\Backend;
use fast\Tree;
use think\Cache;
use \PHPExcel;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use think\Config;
use think\Loader;

/**
 * 规则管理
 *
 * @icon   fa fa-list
 * @remark 规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过控制台进行生成规则节点
 */
class Rule extends Backend
{

    /**
     * @var \app\admin\model\AuthRule
     */
    protected $model = null;
    protected $rulelist = [];
    protected $multiFields = 'ismenu,status';
    protected $addon_name = "";
    protected $first_menu = '';//插件的顶级菜单规则
    protected $addon_menu_id = 0;
    protected $addon_info;

    public function _initialize()
    {
        $this->addon_name = $this->request->param('name');
        if ($this->addon_name) {
            session('rule_addon_name', $this->addon_name);
        } else {
            $this->addon_name = session('rule_addon_name');
        }
        if (!$this->addon_name) {
            $this->error("插件菜单有误");
        }
        //处理插件的顶级菜单规格
        $this->addon_info = get_addon_info($this->addon_name);
        $this->first_menu = isset($this->addon_info['first_menu']) ? $this->addon_info['first_menu'] : $this->addon_name;
        parent::_initialize();
        $this->model = model('AuthRule');
        // 必须将结果集转换为数组
        $ruleList = collection($this->model->order('weigh', 'desc')->order('id', 'asc')->select())->toArray();
        foreach ($ruleList as $k => &$v) {
            if ($this->first_menu == $v['name']) {
                $this->addon_menu_id = $v['id'];
            }
            $v['title'] = __($v['title']);
            $v['remark'] = __($v['remark']);
        }
        unset($v);
        $ruledata = [0 => __('None')];
        if ($this->addon_menu_id) {
            Tree::instance()->init($ruleList);
            Tree::instance()->init(Tree::instance()->getChildren($this->addon_menu_id, true));
            $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
            foreach ($this->rulelist as $k => &$v) {
                if (!$v['ismenu']) {
                    continue;
                }
                $ruledata[$v['id']] = $v['title'];
            }
            unset($v);
        }
        $this->view->assign('addon_name', $this->addon_name);
        $this->view->assign('first_menu', $this->first_menu);
        $this->view->assign('ruledata', $ruledata);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list = $this->rulelist;
            $total = count($this->rulelist);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($this->rulelist && !$params['pid']) {
                $this->error("请选择父类");
            }
            if (!$this->rulelist) {
                //更新配置
                $this->addon_info['first_menu'] = $params['name'];
                set_addon_info($this->addon_name, $this->addon_info);
            }
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    $this->error(__('必须选择父类或是菜单'));
                }
                $result = $this->model->validate()->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                Cache::rm('__menu__');
                $this->success();
            }
            $this->error();
        }
        return $this->view->fetch('', ['pid' => $this->request->param('ids', '0', 'intval')]);
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a", [], 'strip_tags');
            if ($params) {
                if (!$params['ismenu'] && !$params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                if ($params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(collection(AuthRule::select())->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        $this->error(__('Can not change the parent to child'));
                    }
                }
                //这里需要针对name做唯一验证
                $ruleValidate = \think\Loader::validate('AuthRule');
                $ruleValidate->rule([
                    'name' => 'require|format|unique:AuthRule,name,' . $row->id,
                ]);
                $result = $row->validate()->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                Cache::rm('__menu__');
                $this->success();
            }
            $this->error();
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->delete();
            if ($count) {
                Cache::rm('__menu__');
                $this->success();
            }
        }
        $this->error();
    }

    /**
     * 一键生成菜单
     * @return string|void
     * @throws \think\Exception
     */
    public function import()
    {
        if ($this->request->isPost()) {
            $this->execute();

        }
        return $this->view->fetch('', ['name' => $this->addon_name]);
    }

    /**
     *
     */
    protected function execute()
    {
        //当前的插件后台目录
        $adminPath = dirname(dirname(__DIR__)) . DS . $this->addon_name . DS;
        //控制器名
        $controller = $this->request->param('controller') ?: '';
        if (!$controller) {
            //如果不存在就是批量生成
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($adminPath), \RecursiveIteratorIterator::LEAVES_ONLY
            );
            $controller = [];
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $controller[] = str_replace(array(dirname($adminPath) . DS, "\\", '.php'), array("", "/", ''), $file->getRealPath());
                }
            }
        } else {
            $controller = explode('|', $controller);//分割成数组
        }
        if (!$controller) {
            $this->error("当前插件没有相应的控制器");
        }
        $force = $this->request->param('force');
        //是否为删除模式
        $delete = $this->request->param('delete');
        //是否控制器完全匹配
        $equal = $this->request->param('equal');

        if ($delete) {
            //TODO不做
        }
        foreach ($controller as $index => $item) {
            if (stripos($item, '_') !== false) {
                $item = Loader::parseName($item, 1);
            }
            if (stripos($item, '/') !== false) {
                $controllerArr = explode('/', $item);
                end($controllerArr);
                $key = key($controllerArr);
                $controllerArr[$key] = ucfirst($controllerArr[$key]);
            } else {
                $controllerArr = [ucfirst($item)];
            }
            $adminPath = dirname(dirname(__DIR__)) . DS . implode(DS, $controllerArr) . '.php';
            if (!is_file($adminPath)) {
                $this->error('找不到控制器：' . $item);
                return;
            }
            $this->importRule($item);
        }
        Cache::rm("__menu__");
        $this->success("生成成功!");
    }

    /**
     * 递归扫描文件夹
     * @param string $dir
     * @return array
     */
    protected function scandir($dir)
    {
        $result = [];
        $cdir = scandir($dir);
        foreach ($cdir as $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DS . $value)) {
                    $result[$value] = $this->scandir($dir . DS . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * 导入规则节点
     * @param array $dirarr
     * @param array $parentdir
     * @return array
     */
    protected function importIng($dirarr, $parentdir = [])
    {
        $menuarr = [];
        foreach ($dirarr as $k => $v) {
            if (is_array($v)) {
                //当前是文件夹
                $nowparentdir = array_merge($parentdir, [$k]);
                $this->importIng($v, $nowparentdir);
            } else {
                //只匹配PHP文件
                if (!preg_match('/^(\w+)\.php$/', $v, $matchone)) {
                    continue;
                }
                //导入文件
                $controller = ($parentdir ? implode('/', $parentdir) . '/' : '') . $matchone[1];
                $this->importRule($controller);
            }
        }

        return $menuarr;
    }

    protected function importRule($controller)
    {
        $controller = str_replace('\\', '/', $controller);
        if (stripos($controller, '/') !== false) {
            $controllerArr = explode('/', $controller);
            end($controllerArr);
            $key = key($controllerArr);
            $controllerArr[$key] = ucfirst($controllerArr[$key]);
        } else {
            $key = 0;
            $controllerArr = [ucfirst($controller)];
        }
        $classSuffix = Config::get('controller_suffix') ? ucfirst(Config::get('url_controller_layer')) : '';
        $className = "\\app\\admin\\controller\\" . implode("\\", $controllerArr) . $classSuffix;

        $pathArr = $controllerArr;
        array_unshift($pathArr, '', 'application', 'admin', 'controller');
        $classFile = ROOT_PATH . implode(DS, $pathArr) . $classSuffix . ".php";
        $classContent = file_get_contents($classFile);
        $uniqueName = uniqid("FastAdmin") . $classSuffix;
        $classContent = str_replace("class " . $controllerArr[$key] . $classSuffix . " ", 'class ' . $uniqueName . ' ', $classContent);
        $classContent = preg_replace("/namespace\s(.*);/", 'namespace ' . __NAMESPACE__ . ";", $classContent);

        //临时的类文件
        $className = __DIR__ . DS . $uniqueName;
        $tempClassFile = $className . ".php";
        file_put_contents($tempClassFile, $classContent);
        //反射机制调用类的注释和方法名
        $className = "\\app\\admin\\controller\\development\\auth\\" . $uniqueName;

        $reflector = new \ReflectionClass($className);
        if (isset($tempClassFile)) {
            //删除临时文件
            @unlink($tempClassFile);
        }

        //只匹配公共的方法
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $classComment = $reflector->getDocComment();


        //判断是否有启用软删除
        $softDeleteMethods = ['destroy', 'restore', 'recyclebin'];
        $withSofeDelete = false;
        $modelRegexArr = ["/\\\$this\->model\s*=\s*model\(['|\"](\w+)['|\"]\);/", "/\\\$this\->model\s*=\s*new\s+([a-zA-Z\\\]+);/"];
        $modelRegex = preg_match($modelRegexArr[0], $classContent) ? $modelRegexArr[0] : $modelRegexArr[1];
        preg_match_all($modelRegex, $classContent, $matches);
        if (isset($matches[1]) && isset($matches[1][0]) && $matches[1][0]) {
            \think\Request::instance()->module('admin');
            $model = model($matches[1][0]);
            if (in_array('trashed', get_class_methods($model))) {
                $withSofeDelete = true;
            }
        }
        //忽略的类
        if (stripos($classComment, "@internal") !== false) {
            return;
        }
        preg_match_all('#(@.*?)\n#s', $classComment, $annotations);
        $controllerIcon = 'fa fa-circle-o';
        $controllerRemark = '';
        //判断注释中是否设置了icon值
        if (isset($annotations[1])) {
            foreach ($annotations[1] as $tag) {
                if (stripos($tag, '@icon') !== false) {
                    $controllerIcon = substr($tag, stripos($tag, ' ') + 1);
                }
                if (stripos($tag, '@remark') !== false) {
                    $controllerRemark = substr($tag, stripos($tag, ' ') + 1);
                }
            }
        }

        //过滤掉其它字符
        $controllerTitle = trim(preg_replace(array('/^\/\*\*(.*)[\n\r\t]/u', '/[\s]+\*\//u', '/\*\s@(.*)/u', '/[\s|\*]+/u'), '', $classComment));

        //导入中文语言包
        \think\Lang::load(dirname(__DIR__) . DS . 'lang/zh-cn.php');

        //先导入菜单的数据
        $pid = 0;
        foreach ($controllerArr as $k => $v) {
            $key = $k + 1;
            //驼峰转下划线
            $controllerNameArr = array_slice($controllerArr, 0, $key);
            foreach ($controllerNameArr as &$val) {
                $val = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $val), "_"));
            }
            unset($val);
            $name = implode('/', $controllerNameArr);
            $title = (!isset($controllerArr[$key]) ? $controllerTitle : '');
            $icon = (!isset($controllerArr[$key]) ? $controllerIcon : 'fa fa-list');
            $remark = (!isset($controllerArr[$key]) ? $controllerRemark : '');
            $title = $title ? $title : $v;

            $rulemodel = $this->model->get(['name' => $name]);
            if (!$rulemodel) {
                $this->model
                    ->data(['pid' => $pid, 'name' => $name, 'title' => $title, 'icon' => $icon, 'remark' => $remark, 'ismenu' => 1, 'status' => 'normal'])
                    ->isUpdate(false)
                    ->save();
                $pid = $this->model->id;
            } else {
                $pid = $rulemodel->id;
            }
        }
        $ruleArr = [];
        foreach ($methods as $m => $n) {
            //过滤特殊的类
            if (substr($n->name, 0, 2) == '__' || $n->name == '_initialize') {
                continue;
            }
            //未启用软删除时过滤相关方法
            if (!$withSofeDelete && in_array($n->name, $softDeleteMethods)) {
                continue;
            }
            //只匹配符合的方法
            if (!preg_match('/^(\w+)' . Config::get('action_suffix') . '/', $n->name, $matchtwo)) {
                unset($methods[$m]);
                continue;
            }
            $comment = $reflector->getMethod($n->name)->getDocComment();
            //忽略的方法
            if (stripos($comment, "@internal") !== false) {
                continue;
            }
            //过滤掉其它字符
            $comment = preg_replace(array('/^\/\*\*(.*)[\n\r\t]/u', '/[\s]+\*\//u', '/\*\s@(.*)/u', '/[\s|\*]+/u'), '', $comment);

            $title = $comment ? $comment : ucfirst($n->name);

            //获取主键，作为AuthRule更新依据
            $id = $this->getAuthRulePK($name . "/" . strtolower($n->name));

            $ruleArr[] = array('id' => $id, 'pid' => $pid, 'name' => $name . "/" . strtolower($n->name), 'icon' => 'fa fa-circle-o', 'title' => $title, 'ismenu' => 0, 'status' => 'normal');
        }

        $this->model->isUpdate(false)->saveAll($ruleArr);
    }

    //获取主键
    protected function getAuthRulePK($name)
    {
        if (!empty($name)) {
            $id = $this->model
                ->where('name', $name)
                ->value('id');
            return $id ? $id : null;
        }
    }

}

