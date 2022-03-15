<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

namespace app\admin\controller\templates;

use addons\templates\library\Service;
use app\common\controller\Backend;
use think\Config;


/**
 *模板管理
 */
class Templates extends Backend
{

    protected $noNeedRight = ['select'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 模板列表
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            //获取配置文件
            $config = config('self_template');
            $template_list = array();
            //获取所有插件模板
            $addon_template_dir = ROOT_PATH . "public" . DS . "templates" . DS . 'addons' . DS;
            $config_addons = isset($config['addons']) ? $config['addons'] : [];

            if (($handle = @opendir($addon_template_dir)) != NULL) {
                while (false !== ($name = readdir($handle))) {
                    if ($name == '.' || $name == '..') continue;
                    $path = $addon_template_dir . $name . DS;
                    if (@is_dir($path) && is_file($path . 'info.ini')) {
                        $info = Config::parse($path . 'info.ini', '', $path);//获取模板配置信息
                        $info['module'] = 0;//0是插件模板1是模块模板
                        $info['is_use'] = 0;
                        foreach ($config_addons as $key => $row) {
                            if ($row['name'] == $info['name']) {
                                $info['is_use'] = 1;
                                break;
                            }
                        }
                        //适用的插件
                        $info['can_use'] = $this->select($info['name'], $info['module'], 'array');
                        $template_list[] = $info;
                    }
                }
                closedir($handle);
            }
            //获取所有模块模板
            $module_template_dir = ROOT_PATH . "public" . DS . "templates" . DS . 'modules' . DS;
            $config_addons = isset($config['modules']) ? $config['modules'] : [];
            if (($handle = @opendir($module_template_dir)) != NULL) {
                while (false !== ($name = readdir($handle))) {
                    if ($name == '.' || $name == '..') continue;
                    $path = $module_template_dir . $name . DS;

                    if (@is_dir($path) && is_file($path . 'info.ini')) {
                        $info = Config::parse($path . 'info.ini', '', $path);//获取模板配置信息
                        $info['module'] = 1;//0是插件模板1是模块模板
                        $info['is_use'] = 0;
                        foreach ($config_addons as $key => $row) {
                            if ($row['name'] == $info['name']) {
                                $info['is_use'] = 1;
                                break;
                            }
                        }
                        //适用的模块
                        $info['can_use'] = $this->select($info['name'], $info['module'], 'array');
                        $template_list[] = $info;
                    }
                }
                closedir($handle);
            }

            $result = array("total" => count($template_list), "rows" => $template_list);
            return json($result);
        }
        return $this->view->fetch();

    }

    /**
     * 创建模板
     * @return string
     * @throws \think\Exception
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post('row/a');
            $service = new  Service();
            if ($service->create($data)) {
                $this->success("成功");
            } else {
                $this->error("失败");
            }

        } else {
            return $this->view->fetch();
        }
    }

    /**
     * 使用模板
     * @param null $name 模板name
     * @return string
     * @throws \think\Exception
     */
    public function edit($name = NULL)
    {
        $is_module = $this->request->param('module', 0, 'intval');//1是模块0是插件
        $temp_name = $is_module == 1 ? "modules" : "addons";
        //获取配置
        //获取配置文件
        $templates = config('self_template');

        if ($this->request->isPost()) {

            $param = $this->request->param('row/a');
            if (isset($param['domain']) && $param['domain'] && !preg_match("/^[A-Za-z0-9]+$/i", $param['domain'])) {
                $this->error('二级域名只能是数字和字母');
            }
            //获取模板信息
            $path = ROOT_PATH . "public" . DS . 'templates' . DS . $temp_name . DS . $name . DS . 'info.ini';
            if (!is_file($path)) {
                $this->error('模板配置文件不存在');
            }
            $info = Config::parse($path, '', $path);
            $info['view_base'] = '../public/templates/' . $temp_name . DS . $name . DS;

            $select_name_arr = explode(',', $param['select_name']);

            $temp_arr = array();
            foreach ($select_name_arr as $val) {
                $val = $val . ($param['domain'] ? "_" . $param['domain'] : "");
                $temp_arr[$val] = $info;
            }

            //移除之前的
            if (isset($templates[$temp_name])) {
                foreach ($templates[$temp_name] as $key => &$row) {
                    if ($row['name'] == $name && !isset($temp_arr[$key])) {
                        unset($templates[$temp_name][$key]);
                    }
                }
            }


            $templates[$temp_name] = array_merge(isset($templates[$temp_name]) ? $templates[$temp_name] : [], $temp_arr);
            $file = APP_PATH . "extra" . DS . 'self_template.php';
            if (!file_exists($file)) {
                file_put_contents($file, '');
            }
            if (!is_really_writable($file)) {
                $this->error('文件没有写入权限');
            }
            if ($handle = fopen($file, 'w')) {
                fwrite($handle, "<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/\n\n" . "return " . var_export($templates, TRUE) . ";\n");
                fclose($handle);
            } else {
                $this->error('文件没有写入权限');
            }
            $this->success();
        }
        $select_name = '';
        $domain = '';
        if (isset($templates[$temp_name])) {
            foreach ($templates[$temp_name] as $key => $row) {
                if ($row['name'] == $name){
                    //分解二级域名
                    $key_arr = explode('_', $key);
                    if ((count($key_arr) > 1)) {
                        $key = $key_arr[0];
                        $domain = $key_arr[1];
                    }
                    $select_name = $select_name ? $select_name . ',' . $key : $key;
                }
            }
        }
        return $this->view->fetch('', ['name' => $name, 'module' => $is_module, 'select_name' => $select_name, 'domain' => $domain]);
    }

    /**
     * 选择模块或插件
     * @param null $name 模板标识
     * @param null $module 1模块0插件
     * @param null $type json or array
     * @return bool|\think\response\Json
     */
    public function select($name = null, $module = null, $return_type = 'json')
    {
        $name = $name != null ? $name : $this->request->param('name');//模板标识
        $module = $module != null ? $module : $this->request->param('module');//模块or插件
        $keyValue = $this->request->param('keyValue');//
        $keyValueArr = $keyValue ? explode(',', $keyValue) : [];

        $temp_name = $module == 1 ? "modules" : "addons";

        $dir = ROOT_PATH . 'public' . DS . 'templates' . DS . $temp_name . DS . $name . DS;
        $lists = [];
        if (($handle = @opendir($dir)) == NULL) {
            if ($return_type == 'array') {
                return [];
            }
            return json(['list' => $lists, 'total' => count($lists)]);
        };
        while (false !== ($path_name = readdir($handle))) {
            if ($path_name == '.' || $path_name == '..') continue;
            $path = $dir . $path_name;
            if ($keyValueArr) {
                if (in_array($path_name, $keyValueArr) && @is_dir($path)) {
                    $temp['id'] = $temp['name'] = $path_name;
                    $lists[] = $temp;
                }
            } else {
                if (@is_dir($path)) {
                    $temp['id'] = $temp['name'] = $path_name;
                    $lists[] = $temp;
                }
            }

        }
        closedir($handle);
        if ($return_type == 'array') {
            return $lists ? $lists : [];
        }
        return json(['list' => $lists, 'total' => count($lists)]);


    }

    /**
     * 删除模板
     * @param string $ids
     */
    public function del($ids = "")
    {
        $is_module = $this->request->param('module', 0, 'intval');//1是模块0是插件
        $name = $this->request->param('name');
        $temp_name = $is_module == 1 ? "modules" : "addons";

        if (!$name) $this->error('模板不存在');

        //获取模板信息
        $path = ROOT_PATH . "public" . DS . 'templates' . DS . $temp_name . DS . $name . DS;
        if (!is_dir($path)) {
            $this->error('模板目录不存在');
        }
        //判断模板是否使用中，如果使用中不给删除
        $self_template = config('self_template.' . $temp_name);
        if ($self_template) {
            foreach ($self_template as $row) {
                if ($row['name'] == $name) {
                    $this->error('使用中的模板不能删除');
                }
            }

        }
        //删除模板
        rmdirs($path);
        //删除资源文件
        $assets = ROOT_PATH . "public" . DS . 'templates' . DS . 'assets' . DS . $temp_name . DS . $name . DS;
        if (!is_dir($assets)) {
            rmdirs($assets);
        }
        $this->success('删除成功');

    }

    /**
     * 模板打包
     * @param null $name
     * @return string
     * @throws \think\Exception
     */
    public function package($name = NULL)
    {
        $is_module = $this->request->param('module', 0, 'intval');//1是模块0是插件
        $name = $this->request->param('name');
        $temp_name = $is_module == 1 ? "modules" : "addons";

        if (!$name) $this->error('模板不存在');

        //获取模板信息
        $templates_relativePath = str_replace(DS, '/',"public" . DS . "templates" . DS . $temp_name . DS . $name . DS);
        $assets_relativePath = str_replace(DS, '/',"public" . DS . "templates" . DS . 'assets' . DS . $temp_name . DS . $name . DS);
        $path = ROOT_PATH . $templates_relativePath . 'info.ini';
        if (!is_file($path)) {
            $this->error('模板配置文件不存在');
        }
        $config_info = Config::parse($path, '', $path);

        if ($this->request->isPost()) {
            $post_data = $this->request->post('row/a');
            if (!isset($post_data['version']) || !preg_match("/^\d+\.\d+\.\d+$/i", $post_data['version'])) {
                $this->error(__('模板版本号不规范'));
            }
            $config_info['version'] = $post_data['version'];
            //移动项目更新的文件end
            $tmp_dir = RUNTIME_PATH . 'templates' . DS . $temp_name . DS;
            if (!is_dir($tmp_dir)) {
                @mkdir($tmp_dir, 0755, true);
            }

            $templates_file = $tmp_dir . $temp_name . '-' . $name . '-' . $config_info['version'] . '.zip';
            if (!class_exists('ZipArchive')) {
                $this->error(__('ZinArchive 没有安装'));
            }


            $template_dir = ROOT_PATH . $templates_relativePath;
            $template_assets_dir = ROOT_PATH . $assets_relativePath;

            $zip = new \ZipArchive;
            $zip->open($templates_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            try {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($template_dir), \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $filename => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = str_replace(DS, '/', substr($filePath, strlen($template_dir)));
                        $zip->addFile($filePath, $templates_relativePath . $relativePath);
                    }
                }
                unset($files);
            } catch (\Exception $e) {
                $this->error("打包模板出错");
            }
            //打包资源
            try {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($template_assets_dir), \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = str_replace(DS, '/', substr($filePath, strlen($template_assets_dir)));
                        $zip->addFile($filePath, $assets_relativePath . $relativePath);
                    }
                }
            } catch (\Exception $e) {
                $this->error("打包资源出错");
            }

            $zip->close();
            $this->success("打包成功");
        }
        return $this->view->fetch('', ['name' => $name, 'info' => $config_info, 'temp_name' => $temp_name]);

    }

    /**
     * 上传安装模板
     */
    public function local()
    {
        Config::set('default_return_type', 'json');
        $file = $this->request->file('file');
        //上传文件临时存放目录
        $tmpDir = RUNTIME_PATH . 'templates' . DS;
        if (!is_dir($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        $info = $file->rule('uniqid')->validate(['size' => 10240000, 'ext' => 'zip'])->move($tmpDir);
        if ($info) {
            $tmpName = substr($info->getFilename(), 0, stripos($info->getFilename(), '.'));
            try {
                $temp_unzip = $this->unzip($tmpName, $tmpDir);

                //安全判断 php后缀都当成非法文件不给安装
                $suffix = ['php'];
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($temp_unzip), \RecursiveIteratorIterator::LEAVES_ONLY
                );

                $infoFile = "";//info文件路径
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        if (in_array($file->getExtension(), $suffix)) {
                            @rmdirs($temp_unzip);
                            throw exception("含非法文件不给安装");
                        }
                        if ($file->getFilename() == 'info.ini'|| strstr($file->getFilename(), 'info.ini')) {
                            $infoFile = $name;
                        }
                    }
                }
                unset($files);
                if (!is_file($infoFile)) {
                    @rmdirs($temp_unzip);
                    throw exception("模板配置文件不存在");
                }
                //移动至模板目录
                copydirs($temp_unzip, ROOT_PATH);
                @rmdirs($temp_unzip);
                unset($info);
                @unlink($tmpDir . $tmpName . '.zip');
            } catch (Exception $e) {
                unset($info);
                @unlink($tmpDir . $tmpName . '.zip');
                $this->error(__($e->getMessage()));
            }
        } else {
            // 上传失败获取错误信息
            $this->error(__($file->getError()));
        }

        $this->success("安装成功");


    }

    /**
     * 解压
     *
     * @param string $name 待解压文件名
     * @param string $tmpDir 待解压文件路径
     * @return  string 返回解压目录
     */
    private function unzip($name, $tmpDir)
    {
        $file = $tmpDir . $name . '.zip';
        $dir = $tmpDir . $name . DS;
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive;
            if ($zip->open($file) !== TRUE) {
                $this->error('Unable to open the zip file');
            }
            if (!$zip->extractTo($dir)) {
                $zip->close();
                $this->error('Unable to extract the file');
            }
            $zip->close();
            @unlink($file);
            return $dir;
        }
        $this->error("无法执行解压操作，请确保ZipArchive安装正确");
    }

    /**
     * 重置模板
     */
    public function reset()
    {
        $file = APP_PATH . "extra" . DS . 'self_template.php';
        if (!file_exists($file)) {
            file_put_contents($file, '');
        }
        if (!is_really_writable($file)) {
            $this->error('文件没有写入权限');
        }
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/\n\n" . "return " . var_export([], TRUE) . ";\n");
            fclose($handle);
        } else {
            $this->error('文件没有写入权限');
        }
        $this->success("重置成功");
    }

    /**
     * 模板配置
     */
    public function config($name = NULL)
    {
        $is_module = $this->request->param('module', 0, 'intval');//1是模块0是插件
        $temp_name = $is_module == 1 ? "modules" : "addons";

        if (!$name) $this->error('模板不存在');

        //获取模板信息
        $service = new  Service();
        $config = $service->getConfig($name, $temp_name);

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
                    if (!$service->setConfig($name, $temp_name, $config)) {
                        throw exception($service->getError());
                    }
                } catch (\Exception $e) {
                    $this->error(__($e->getMessage()));
                }
                $this->success('ok');
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign('typeList', \app\common\model\Config::getTypeList());
        $this->view->assign("templates", ['info' => [], 'config' => $config]);
        return $this->view->fetch('', ['name' => $name, 'is_module' => $is_module]);
    }

    /**
     * 添加配置
     */
    public function addConfig()
    {
        if ($this->request->isPost()) {
            $name = $this->request->param("name");
            $is_module = $this->request->param('module', 0, 'intval');//1是模块0是插件
            $temp_name = $is_module == 1 ? "modules" : "addons";

            if (!$name) $this->error('模板不存在');

            //获取模板信息
            //获取模板信息
            $service = new  Service();
            $config = $service->getConfig($name, $temp_name);

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

                    //更新配置文件
                    if (!$service->setConfig($name, $temp_name, $config)) {
                        throw exception($service->getError());
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->success("ok");
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->error("非法请求");

    }

}
