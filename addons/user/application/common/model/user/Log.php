<?php

namespace app\common\model\user;

use app\common\library\UserAuth as Auth;
use think\Cookie;
use think\Model;

class Log extends Model
{
    protected static $title = '';

    // 开启自动写入时间戳字段
    protected static $content = '';
    // 定义时间戳字段名
    protected $name = 'user_log';
    protected $autoWriteTimestamp = 'int';
    //自定义日志标题
    protected $createTime = 'createtime';
    //自定义日志内容
    protected $updateTime = '';

    public static function setTitle($title)
    {
        self::$title = $title;
    }

    public static function setContent($content)
    {
        self::$content = $content;
    }

    /*
     * 日志记录
     */
    public static function record($title = '')
    {
        $auth = Auth::instance();
        $token = request()->server('HTTP_TOKEN', request()->request('token', Cookie::get('token')));
        $auth->init($token);
        $user_id = $auth->isLogin() ? $auth->id : 0;
        $username = $auth->isLogin() ? $auth->username : __('Unknown');
        $content = self::$content;
        if (!$content) {
            $content = request()->param();
            foreach ($content as $k => $v) {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
                    unset($content[$k]);
                }
            }
        }
        $title = self::$title;
        if (!$title) {
            $title = [];
            $breadcrumb = Auth::instance()->getBreadCrumb();
            if ($breadcrumb) {
                foreach ($breadcrumb as $k => $v) {
                    $title[] = $v['title'];
                }
            }
            $title = implode(' ', $title);
        }
        self::create([
            'title' => $title,
            'content' => !is_scalar($content) ? json_encode($content) : $content,
            'url' => request()->url(),
            'user_id' => $user_id,
            'username' => $username,
            'useragent' => request()->server('HTTP_USER_AGENT'),
            'ip' => request()->ip()
        ]);
    }

}
