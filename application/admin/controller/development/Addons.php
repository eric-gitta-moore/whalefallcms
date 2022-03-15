<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\development;

use addons\development\library\AddonsService;
use addons\development\library\Menu;
use app\common\controller\Backend;
use think\Controller;
use think\Db;

/**
 *插件管理
 * @author amplam 122795200@qq.com
 * @date   2019年9月25日 16:10:46
 */
class Addons extends Backend
{

    /**
     * 模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['dataTables'];
    protected $searchFields = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 列表
     */
    public function index()
    {

        return $this->view->fetch();

    }


    /**
     * 添加新的插件
     * @return string
     */
    public function add()
    {

        if ($this->request->isPost()) {
            $data = $this->request->post('row/a');
            if (!isset($data['name']) || !preg_match("/^[a-z]+$/i", $data['name'])) {
                $this->error(__('插件信息命名不规范'));
            }
            $new_addon_dir = ADDON_PATH . $data['name'] . DS;
            if (is_dir($new_addon_dir)) {
                $this->error("{$data['name']}插件标识已经存在");
            }
            $data['first_menu'] = $data['name'];//设置默认的插件菜单
            $data['addon_menu'] = '';//菜单初始为空
            $data['url'] = 'addons/' . $data['name'];//默认插件url
            $addonsService = new AddonsService();
            if ($addonsService->create($data)) {
                //在项目生成默认文件目录
                if ($data['copydirs'] == 1) {
                    $copy_path_arr = $addonsService->getCopyPathArr($data['name']);
                    foreach ($copy_path_arr as $dir) {
                        if (is_dir(ADDON_PATH . $data['name'] . DS . $dir)) {
                            copydirs(ADDON_PATH . $data['name'] . DS . $dir, ROOT_PATH . $dir);
                        }
                    }
                    //处理assets文件夹
                    if (is_dir(ADDON_PATH . $data['name'] . DS . 'assets')) {
                        copydirs(ADDON_PATH . $data['name'] . DS . 'assets', ROOT_PATH . 'public' . DS . 'assets/addons' . DS . $data['name']);
                    }
                }
                $this->success("成功");
            } else {
                $this->error($addonsService->getError());
            }
        } else {
            return $this->view->fetch();
        }

    }

    /**
     * 编辑
     * @param unknown $ids
     * @return string
     */
    public function edit($name = null)
    {
        if ($name) {
            session('rule_addon_name', $name);
        } else {
            $name = session('rule_addon_name');
        }
        //处理插件的顶级菜单规格
        $addon_info = get_addon_info($name);
        if (!$addon_info) {
            $this->error("插件不存在");
        }
        if ($this->request->isPost()) {
            $data = $this->request->post('row/a');
            if ($name != $data['name']) {
                $this->error("插件标识不能修改");
            }
            $addonsService = new AddonsService();
            if ($addonsService->edit($data)) {
                $this->success("成功");
            } else {
                $this->error($addonsService->getError());
            }
        }
        return $this->view->fetch('', ['row' => $addon_info]);
    }

    /**
     * 删除
     * @param string $ids
     */
    public function del($ids = "")
    {
        $this->error();
    }


    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 插件打包
     * @param null $name
     * @return string
     * @throws \think\Exception
     */
    public function package($name = null)
    {
        $addon_info = get_addon_info($name);

        if (!$addon_info) {
            $this->error("插件信息获取失败");
        }

        $addonsService = new AddonsService();

        if ($this->request->isPost()) {
            $post_data = $this->request->post('row/a');
            $name = $post_data['name'];
            $addon_info['version'] = $post_data['version'];
            if (!$name) {
                $this->error("插件不存在");
            }
            //处理插件的顶级菜单规格
            $first_menu = isset($addon_info['first_menu']) ? $addon_info['first_menu'] : $name;
            if (!isset($addon_info['name']) || !preg_match("/^[a-z]+$/i", $addon_info['name']) || $addon_info['name'] != $name) {
                $this->error(__('插件信息命名不规范'));
            }

            if (!isset($addon_info['version']) || !preg_match("/^\d+\.\d+\.\d+$/i", $addon_info['version'])) {
                $this->error(__('插件版本号不规范'));
            }

            set_addon_info($name, $addon_info);//更新版本配置
            $addon_dir = ADDON_PATH . $name . DS;
            $menu_list = Menu::export($first_menu);
            $create_menu = $this->getCreateMenu($menu_list);
            //更新菜单

            $tpl_config = $addonsService->getTplConfig();
            $path_name = $addon_dir . $tpl_config['menu']['path'];
            $addonsService->writeToFile($tpl_config['menu']['file_name'], ['addon_menu' => var_export_short($create_menu, "\t")], $path_name, isset($tpl_config['menu']['asname']) ? $tpl_config['menu']['asname'] : '');

            //兼容官方标准生成的菜单插件
            $addon_file = ADDON_PATH . $name . DS . ucfirst($name) . ".php";
            if (is_file($addon_file)) {
                $addon_file_str = file_get_contents($addon_file);
                if (preg_match("/Menu::create\(\\\$menu\);/", $addon_file_str) && !preg_match("/\\\$menu = include \\\$config_file;/", $addon_file_str)) {
                    $search[] = "Menu::create(\$menu);";
                    $replace[] = <<<EOT
                \$menu=[];
                \$config_file= ADDON_PATH ."{$name}" . DS.'config'.DS. "menu.php";
                if (is_file(\$config_file)) {
                   \$menu = include \$config_file;
                }
                if(\$menu){
                    Menu::create(\$menu);
                }
EOT;
                    $addon_file_str = str_replace($search, $replace, $addon_file_str);
                    file_put_contents($addon_file, $addon_file_str);
                    unset($addon_file_str);
                }
            }
            //兼容官方标准生成的菜单插件end

            //更新数据库安装文件
            $create_table_sql = '';
            $prefix = config('database.prefix');

            if ($post_data['table_name']) {
                $table_list = explode(',', $post_data['table_name']);
                foreach ($table_list as $key => $val) {

                    try {
                        $result = Db::query("SHOW CREATE TABLE `" . $val . "`;");
                        if (isset($result[0]) && isset($result[0]['Create Table'])) {
                            $result[0]['Create Table'] = str_replace(["CREATE TABLE `" . $prefix], ["CREATE TABLE IF NOT EXISTS `__PREFIX__"], $result[0]['Create Table']);
                            $create_table_sql .= $result[0]['Create Table'] . ';' . PHP_EOL . PHP_EOL;
                        }
                    } catch (\think\exception\PDOException $e) {
                        continue;
                    }
                }
                unset($table_list);
            }

            //维护数据库的SQL语句
            if (isset($post_data['update_data'])) {
                $create_table_sql .= $post_data['update_data'];
            }

            file_put_contents(ADDON_PATH . $name . DS . 'install.sql', $create_table_sql);
            //更新数据库end
            //缓存打包配置
            $cache_arr = ['table_name' => $post_data['table_name'], 'self_path' => $post_data['self_path'], 'update_data' => $post_data['update_data']];
            file_put_contents(ADDON_PATH . $name . DS . 'config/cache.php', '<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/ ' . PHP_EOL . ' return ' . var_export_short($cache_arr) . ';');
            //end;

            //移动项目更新的文件
            $copy_path_arr = $addonsService->getCopyPathArr($name);
            foreach ($copy_path_arr as $dir) {
                if (is_dir(ROOT_PATH . $dir)) {
                    copydirs(ROOT_PATH . $dir, ADDON_PATH . $name . DS . $dir);
                }
            }
            //处理assets文件夹
            if (is_dir(ROOT_PATH . 'pblic/assets' . DS . 'addons' . DS . $name)) {
                copydirs(ROOT_PATH . 'pblic' . DS . 'addons' . DS . $name, ADDON_PATH . $name . DS . 'assets');
            }
            //开发者自己定义的文件
            if ($post_data['self_path']) {
                $self_path_arr = explode(PHP_EOL, $post_data['self_path']);
                foreach ($self_path_arr as $dir) {
                    if (is_dir(ROOT_PATH . $dir)) {
                        copydirs(ROOT_PATH . $dir, ADDON_PATH . $name . DS . $dir);
                    } else {
                        if (is_file(ROOT_PATH . $dir)) {
                            if (!is_file(ADDON_PATH . $name . DS . $dir)) {
                                mkdir(dirname(ADDON_PATH . $name . DS . $dir), 0755, true);
                            }

                            copy(ROOT_PATH . $dir, ADDON_PATH . $name . DS . $dir); //支持拷贝独立文件
                        }
                    }
                }
            }

            //移动项目更新的文件end
            $addon_tmp_dir = RUNTIME_PATH . 'addons' . DS;
            if (!is_dir($addon_tmp_dir)) {
                @mkdir($addon_tmp_dir, 0755, true);
            }

            $addon_file = $addon_tmp_dir . $addon_info['name'] . '-' . $addon_info['version'] . '.zip';
            if (!class_exists('ZipArchive')) {
                $this->error(__('ZinArchive 没有安装'));
            }

            $zip = new \ZipArchive;
            $zip->open($addon_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addon_dir), \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = str_replace(DS, '/', substr($filePath, strlen($addon_dir)));
                    if (!in_array($file->getFilename(), ['.git', '.DS_Store', 'Thumbs.db'])) {
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            $zip->close();
            $this->success("打包成功");
        }
        $row = $addonsService->getConfig($name, 'cache');
        return $this->view->fetch('', ['name' => $name, 'row' => $row, 'info' => $addon_info]);

    }

    /**
     * 获取创建菜单的数组
     * @param array $menu
     * @return array
     */
    protected function getCreateMenu($menu)
    {
        foreach ($menu as $k => &$v) {
            $v['sublist'] = $v['childlist'];
            unset($v['id'], $v['pid'], $v['createtime'], $v['updatetime'], $v['weigh']
                , $v['status'], $v['spacer'], $v['childlist']);
            if (count($v['sublist']) > 0) {
                $v['sublist'] = $this->getCreateMenu($v['sublist']);
            }
            if (count($v['sublist']) <= 0) {
                unset($v['sublist']);
            }
        }
        return $menu;
    }

    /**
     *选择数据表
     * @internal
     */
    public function dataTables()
    {
        $name = $this->request->param('name');
        $page_size = $this->request->param('pageSize', '10', 'intval');
        $page = $this->request->param('pageNumber', '1', 'intval');
        $keyValue = $this->request->param('keyValue');

        $limit = (($page - 1) * 10) . ",{$page_size}";
        $database = config('database.database');
        $where = $name ? " AND table_name LIKE '%{$name}%'" : "";
        if ($keyValue) {
            $table_list = explode(',', $keyValue);
            $where = "";
            foreach ($table_list as $key => $val) {
                $where .= $where ? " OR  table_name LIKE '%{$val}%' " : "table_name LIKE '%{$val}%'";
            }
            $where = " AND ( " . $where . ")";
        }
        $count = Db::query("SELECT count(*) count  from information_schema.tables where table_schema='{$database}'  {$where} ");
        $tables = Db::query("select table_name from information_schema.tables where table_schema='{$database}'  {$where} order by table_name limit {$limit};");
        $lists = [];

        $database_key = 'table_name';
        foreach ($tables as $row) {
            $temp['id'] = $temp['name'] = $row[$database_key];
            $lists[] = $temp;
        }
        return json(['list' => $lists, 'total' => (isset($count[0]['count']) ? $count[0]['count'] : 0)]);
    }

    /**
     * 配置
     */
    public function config($name = null)
    {
        $name = $this->request->get("name");
        if (!$name) {
            $this->error("插件不存在");
        }
        if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
            $this->error(__('Addon name incorrect'));
        }
        if (!is_dir(ADDON_PATH . $name)) {
            $this->error(__('Directory not found'));
        }
        $info = get_addon_info($name);
        $config = get_addon_fullconfig($name);
        if (!$info) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                foreach ($config as $k => &$v) {
                    if (isset($params[$v['name']])) {
                        if ($v['type'] == 'array') {
                            $params[$v['name']] = is_array($params[$v['name']]) ? $params[$v['name']] : (array)json_decode($params[$v['name']], true);
                            $value = $params[$v['name']];
                        } else {
                            $value = is_array($params[$v['name']]) ? implode(',', $params[$v['name']]) : $params[$v['name']];
                        }
                        $v['value'] = $value;
                    } else {
                        unset($config[$k]);
                    }
                }
                try {
                    //更新配置文件
                    set_addon_fullconfig($name, $config);
                    \think\addons\Service::refresh();
                    $this->success();
                } catch (Exception $e) {
                    $this->error(__($e->getMessage()));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign('typeList', \app\common\model\Config::getTypeList());
        $this->view->assign("addon", ['info' => $info, 'config' => $config]);
        return $this->view->fetch('', ['name' => $name]);
    }

    /**
     * 添加插件配置
     */
    public function addConfig()
    {
        if ($this->request->isPost()) {
            $name = $this->request->param("addon_name");
            if (!$name) {

                $this->error("插件不存在");
            }
            if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
                $this->error(__('Addon name incorrect'));
            }
            if (!is_dir(ADDON_PATH . $name)) {
                $this->error(__('Directory not found'));
            }
            $info = get_addon_info($name);
            $config = get_addon_fullconfig($name);
            if (!$info) {
                $this->error(__('No Results were found'));
            }
            $params = $this->request->post("row/a");
            if ($params) {
                foreach ($params as $k => &$v) {
                    $v = is_array($v) ? implode(',', $v) : $v;
                }
                try {
                    if (in_array($params['type'], ['select', 'selects', 'checkbox', 'radio', 'array'])) {
                        $params['content'] = \app\common\model\Config::decode($params['content']);
                    } else {
                        $params['content'] = '';
                    }
                    //更新配置文件
                    $config = array_merge($config, array($params));

                    set_addon_fullconfig($name, $config);
                    \think\addons\Service::refresh();
                    $this->success();
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->error("非法请求");

    }


}
