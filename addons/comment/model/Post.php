<?php

namespace addons\comment\model;

use addons\comment\library\Markdown;
use app\common\library\Auth;
use app\common\library\Email;
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;
use think\Model;
use think\Validate;

/**
 * 评论模型
 */
class Post extends Model
{
    protected $name = "comment_post";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    // 追加属性
    protected $append = [
        'create_date',
        'human_date',
    ];
    protected static $config = [];

    //自定义初始化
    protected static function init()
    {
        self::$config = get_addon_config('comment');
    }

    public function getCreateDateAttr($value, $data)
    {
        return datetime($data['createtime']);
    }

    public function getHumanDateAttr($value, $data)
    {
        return human_date($data['createtime']);
    }

    public static function getChildrenCommentList(&$commentList, $site_id, $article_id, $level = 1)
    {
        //如果评论为空
        if (!$commentList) {
            return;
        }
        //如果超过最大层级则不再取数据
        if ($level > self::$config['maxlevel']) {
            return;
        }
        //从评论列表获取ID集合
        $ids = implode(',', array_keys($commentList));
        $status = 'normal';
        $maxSize = self::$config['floorpagesize'];
        $prefix = Config::get('database.prefix');

        //取分组前10条数据
        $sql = "SELECT a.id FROM {$prefix}comment_post AS a,
                    (SELECT GROUP_CONCAT(id order by id desc) AS ids FROM {$prefix}comment_post WHERE status='{$status}' AND site_id='{$site_id}' AND article_id='{$article_id}' GROUP BY pid) AS b
                    WHERE FIND_IN_SET(a.id, b.ids) BETWEEN 1 AND {$maxSize} AND status='{$status}' AND site_id='{$site_id}' AND article_id='{$article_id}' AND a.pid IN ({$ids}) ORDER BY a.pid ASC, a.id ASC";
        $list = Db::query($sql);
        if ($list) {
            $ids = [];
            foreach ($list as $index => $item) {
                $ids[] = $item['id'];
            }
            $childrenList = [];
            $result = self::where('id', 'in', $ids)->with(['userinfo'])->field('id,pid,content,user_id,likes,comments,createtime')->select();
            $result = collection($result)->toArray();
            foreach ($result as $index => $item) {
                $item['children'] = [];
                $childrenList[$item['id']] = $item;
            }
            if ($childrenList) {
                self::getChildrenCommentList($childrenList, $site_id, $article_id, $level + 1);
            }
            foreach ($childrenList as $index => $item) {
                $commentList[$item['pid']]['children'][] = $item;
            }
        }
    }

    /**
     * Emoji表情替换
     * @param $content
     * @return null|string|string[]
     */
    public static function emoji($content)
    {
        $config = get_addon_config('comment');
        $emoji = Cache::get("commentemojidata");
        if (!$emoji) {
            $emoji = [];
            $json = (array)json_decode(file_get_contents(ADDON_PATH . 'comment' . DS . 'data' . DS . 'emoji.json'), true);
            foreach ($json as $index => $item) {
                $emoji = array_merge($emoji, $item);
            }
            Cache::set("commentemojidata", $emoji);
        }
        $content = preg_replace_callback("/(\[(.*?)\])/i", function ($match) use ($emoji, $config) {
            if (isset($emoji[$match[0]])) {
                $img = cdnurl("/assets/addons/comment/emoji/" . (substr($match[0], 0, 5) == 'icon_' ? 'wordpress' : 'normal') . "/" . $emoji[$match[0]]);
                if ($config['markdown']) {
                    return "![]({$img})";
                } else {
                    return '<img src="' . $img . '" > ';
                }
            }
        }, $content);
        return $content;
    }

    /**
     * 关联会员模型
     */
    public function userinfo()
    {
        return $this->belongsTo("app\common\model\User")->field('id,nickname,avatar')->setEagerlyType(1);
    }
}
