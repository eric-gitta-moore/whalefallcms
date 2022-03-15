<?php

namespace addons\templates\library;


use think\Config;

/**
 * 助手
 * @author amplam 122795200@qq.com
 * @date 2019年12月3日 09:52:52
 */
class Service
{

    protected $error = "";
    // 插件配置作用域
    protected $configRange = 'templatesconfig';

    /**
     * 构造函数
     * @access public
     */
    public function __construct()
    {

    }

    /**
     * 创建模板
     */
    public function create($data)
    {
        if (!isset($data['name']) || !preg_match("/^[a-z]+$/i", $data['name'])) {
            $this->error = __('模板标识命名不规范');
            return false;
        }
        $temp_name = $data['module'] == 1 ? "modules" : "addons";
        $new_dir = ROOT_PATH . 'public' . '/' . 'templates' . '/' . $temp_name . '/' . $data['name'] . '/';
        if (is_dir($new_dir)) {
            $this->error = ("{$data['name']}模板标识已经存在");
            return false;
        }
        //创建件静态资源文件夹
        $public_dir = ROOT_PATH . 'public' . '/' . 'templates' . '/' . "assets" . '/' . $temp_name . '/' . $data['name'] . '/';
        if (!is_dir($public_dir)) {
            mkdir($public_dir, 0755, true);
        }
        //创建info.ini
        return $this->writeToFile('info.ini', $data, $new_dir);
    }

    /**
     * 获取失败的信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 获取模板的配置数组[完整的]
     * @param string $name 模板名称
     * @param string $type addons、modules 模板
     * @return array
     */
    public function getConfig($name, $type = "addons")
    {

        //获取模板配置信息
        $config_file = ROOT_PATH . "public" . '/' . "templates" . '/' . $type . '/' . $name . '/' . 'config.ini';
        $config = [];
        if (is_file($config_file)) {
            $config = include $config_file;
        } else {
            file_put_contents($config_file, "<?php\n\n" . "return [];\n");//先创建文件
            return [];
        }
        return $config;
    }

    /**
     * 获取系统格式的配置
     * @return array|mixed
     */
    public function getConfigKeyVal($name, $type = "addons")
    {
        if (Config::has($name, $this->configRange)) {
            return Config::get($name, $this->configRange);
        }
        $config = [];
        $config_file = ROOT_PATH . "public/templates/" . $type . '/' . $name . '/config.ini';
        if (is_file($config_file)) {
            $temp_arr = include $config_file;
            foreach ($temp_arr as $key => $value) {
                $config[$value['name']] = $value['value'];
            }
            unset($temp_arr);
        }
        Config::set($name, $config, $this->configRange);

        return $config;
    }

    /**
     * 设置配置数据
     * @param $name
     * @param array $value
     * @return array
     */
    public function setConfig($name, $type = "addons", $value = [])
    {
        //获取模板配置信息
        $config_file = ROOT_PATH . "public" . '/' . "templates" . '/' . $type . '/' . $name . '/' . 'config.ini';
        //更新配置文件
        if (!is_really_writable($config_file)) {
            $this->error = "文件没有写入权限";
            return false;
        }
        if ($handle = fopen($config_file, 'w')) {
            fwrite($handle, "<?php\n\n" . "return " . var_export($value, TRUE) . ";\n");
            fclose($handle);
        } else {
            $this->error = "文件没有写入权限";
            return false;
        }
        return true;
    }


    /**
     * 写入到文件
     * @param string $name 文件名
     * @param array $data 数据
     * @param string $pathname 文件路径
     * @param string $asname 重命名文件
     * @return mixed
     */
    public function writeToFile($name, $data, $pathname, $asname = '')
    {
        $search = $replace = [];
        foreach ($data as $index => &$datum) {
            $search[] = "{\${$index}}";
            $replace[] = $datum;
            $datum = is_array($datum) ? '' : $datum;
        }

        $pathname = str_replace($search, $replace, $pathname);

        unset($datum);
        if (!is_dir($pathname)) {
            mkdir($pathname, 0755, true);
        }
        //没有创建文件
        if ($name == '') return true;
        $content = $this->getReplacedTp($name, $data);
        $name = $asname ? $asname : $name;
        return file_put_contents($pathname . $name, $content);
    }

    /**
     * 获取替换后的数据
     * @param string $name
     * @param array $data
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
        return ADDON_PATH . "templates" . '/' . 'library' . '/' . 'tp' . '/' . $name;
    }


}
