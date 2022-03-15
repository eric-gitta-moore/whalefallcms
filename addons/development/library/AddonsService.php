<?php
namespace addons\development\library;


use think\Config;

/**
 * 插件助手
 * @author amplam 122795200@qq.com
 * @date 2019年9月25日 18:39:20
 */
class AddonsService
{
    protected $tpList=[];
    private $tplConfig=[
        'info'=>['path'=>"",'file_name'=>'info.ini'],
        ['path'=>"",'file_name'=>'config.tpl','asname'=>'config.php'],
        ['path'=>"",'file_name'=>'bootstrap.js'],
        ['path'=>"",'file_name'=>'install.sql'],
        'admin_controller'=>['path'=>"application/admin/controller/{\$name}/",'file_name'=>'indexController.tpl','asname'=>'Index.php'],
        'admin_model'=>['path'=>"application/admin/model/{\$name}",'file_name'=>''],
        'admin_validate'=>['path'=>"application/admin/validate/{\$name}",'file_name'=>''],
        'admin_view'=>['path'=>"application/admin/view/{\$name}",'file_name'=>''],
        'common_model'=>['path'=>"application/common/model/{\$name}",'file_name'=>''],
        'common_validate'=>['path'=>"application/common/validate/{\$name}",'file_name'=>''],
        ['path'=>"assets",'file_name'=>''],
        ['path'=>"controller",'file_name'=>''],
        ['path'=>"data",'file_name'=>''],
        ['path'=>"library",'file_name'=>''],
        'menu'=>['path'=>"config/",'file_name'=>'menu.tpl','asname'=>'menu.php'],
        ['path'=>"model",'file_name'=>''],
        ['path'=>"view",'file_name'=>''],
        'public_backend'=>['path'=>"public/assets/js/backend/{\$name}",'file_name'=>''],
    ]; // 配置
    protected $copyKey=['admin_controller','admin_model','admin_validate','admin_view','common_model','common_validate','public_backend'];

    protected $error="";
    /**
     * 构造函数
     * WxPay constructor.
     * @param $config
     */
    public function __construct($config=[])
    {
        $this->tplConfig = array_merge($this->tplConfig,$config);
    }

    /**
     * 生成文件
     * @param $param
     * @return bool
     */
    public  function create(&$param){

        try{
            foreach ($this->tplConfig as $r){
                $path_name= ADDON_PATH .$param['name'] . DS.$r['path'];
                $this->writeToFile($r['file_name'],$param,$path_name,isset($r['asname'])?$r['asname']:'');
            }
            //生成插件安装控制器
            $param['controllername']=ucfirst($param['name']);
            $this->writeToFile('AddonsInstallTp.tpl',$param,$path_name= ADDON_PATH .$param['name'].DS,($param['controllername'].'.php'));

        }catch (\Exception $e){
            $this->error=$e->getMessage().$e->getLine();
            return  false;
        }

        return true;
    }

    /**
     * 修改插件信息
     * @param $param
     * @return bool
     */
    public  function edit(&$param){
        try{
            $path_name= ADDON_PATH .$param['name'] . DS.$this->tplConfig['info']['path'];
            $this->writeToFile($this->tplConfig['info']['file_name'],$param,$path_name);
        }catch (\Exception $e){
            $this->error=$e->getMessage().$e->getLine();
            return  false;
        }
        return  true;
    }
    /**
     * 获取失败的信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 写入到文件
     * @param string $name
     * @param array  $data
     * @param string $pathname
     * @return mixed
     */
    public function writeToFile($name, $data, $pathname,$asname='')
    {
        $search = $replace = [];
        foreach ($data as $index => &$datum) {
            $search[] = "{\${$index}}";
            $replace[] = $datum;
            $datum = is_array($datum) ? '' : $datum;
        }

        $pathname = str_replace($search, $replace, $pathname);

        unset($datum);if (!is_dir($pathname)) {
            mkdir($pathname, 0755, true);
        }
        //没有创建文件
        if ($name=='') return true;
        $content = $this->getReplacedTp($name, $data);
        $name=$asname?$asname:$name;
        return file_put_contents($pathname.$name, $content);
    }
    /**
     * 获取替换后的数据
     * @param string $name
     * @param array  $data
     * @return string
     */
    protected function getReplacedTp($name, $data)
    {
        foreach ($data as $index => &$datum) {
            $datum = is_array($datum) ? '' : $datum;
        }
        unset($datum);
        $search = $replace = [];
        foreach ($data as $k => $v) {
            $search[] = "{\${$k}}";
            $replace[] = $v;
        }
        $tpname = $this->getTp($name);
        if (isset($this->tpList[$tpname])) {
            $tp = $this->tpList[$tpname];
        } else {
            $this->tpList[$tpname] = $tp = file_get_contents($tpname);
        }

        $content = str_replace($search, $replace, $tp);
        return $content;
    }

    /**
     * 获取模板
     * @param string $name
     * @return string
     */
    protected function getTp($name)
    {
        return ADDON_PATH ."development". DS.'library'. DS.'tp'. DS.$name;
    }

    /**
     * 获取模板配置
     * @return array
     */
    public function getTplConfig(){
        return $this->tplConfig;
    }

    /**
     * 获取插件自定义的配置数组
     * @param string $addon_name 插件模块
     * @return array
     */
    public function getConfig($addon_name,$name)
    {
        $config = [];
        $config_file= ADDON_PATH .$addon_name . DS.'config'.DS. "{$name}.php";
        if (is_file($config_file)) {
            $config = include $config_file;
        }
        return $config;
    }

    /**
     * 获取系统要更新的目录
     * @param string $addon_name
     * @return array
     */
    public function getCopyPathArr($addon_name=''){
        $arr=[];

        foreach ($this->copyKey as $val){
            if (isset($this->tplConfig[$val])){
                $search = $replace = [];
                $search[] = "{\$name}";
                $replace[] = $addon_name;
                $arr[] = str_replace($search, $replace, $this->tplConfig[$val]['path']);
            }
        }
        return $arr;
    }

}
