<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


use app\common\model\User;
use app\common\model\user\Buylog;
use app\common\model\user\Collection;

class UserService
{

    public static function is_sign(User $user)
    {
        $cnt = $user -> signin()  -> whereTime('createtime','today') -> count();
        if ($cnt)
        {
            return true;
        }
        else
            return false;
    }



    /**
     * 获取用户收藏记录
     * @param $user_id
     * @param bool $book_id
     * @param bool $type
     * @return Collection[]|false
     * @throws \think\exception\DbException
     */
    public static function getUserCollection($user_id,$book_id = false, $type = false)
    {
        if (empty($user_id))
            return null;

        $where = [
            'user_id' => $user_id
        ];

        if ($type !== false) {
            $type = strtolower($type);
            $where['status'] = $type;
        }

        if ($book_id !== false) {
            $book_id = intval($book_id);
            $where['bookid'] = $book_id;
        }


        return Collection::all($where);
    }

    /**
     * 获取用户购买书本中章节情况
     * @param int $user_id
     * @param int $book_id
     * @param bool|int $chapter_id 指定章节
     * @param string $type
     * @param bool $only_chapter_array 是否仅返回已购买章节数组
     * @return Buylog[]|array|false|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserBuyBookStatus($user_id,$book_id,$chapter_id=false,$type='cartoon',$only_chapter_array=false)
    {
        if (empty($user_id) || empty($book_id) || empty($type))
            return null;

        $buylog = Buylog::where([
            'user_id' => $user_id,
            'bookid' => $book_id,
            'status' => strtolower($type)
        ]);

        if ($chapter_id!==false)
        {
            $buylog -> where('chapterid',$chapter_id);
        }

        if ($only_chapter_array)
        {
            return $buylog -> column('chapterid');
        }

        return $buylog -> select();

//        $chapter = [];
//        foreach ($buylog as $item) {
//            $chapter[] = $item['chapterid'];
//        }
//        return $chapter;

    }

    /**
     * 给用户购买章节
     * @param $id
     * @param User $user
     * @param string $type
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function buyChapter($id,User $user, $type = 'cartoon')
    {
        if (!empty($id))
            $id = input('id/d');
        if (!empty($type))
            $type = input('type', 'cartoon');
        $type = check_type($type);

        $chapter_model = '\app\common\model\\' . $type . '\Chapter';
        $chapter = $chapter_model::get(['id', $id]);
        if (is_null($chapter)) {
            return '没有该章节';
        }


        if ($user->score < $chapter['money']) {
            session('auto_pay' , 0);
            return config('site.score_name') . __('不足') . ',' . __('已为您关闭自动购买');
        }

        $r = $user->buyLog()->save([
            'bookid' => $chapter[$type . '_' . $type . '_id'],
            'chapterid' => $chapter['id'],
            'status' => $type
        ]);
        if ($r) {
            \app\common\model\User::score(0-intval($chapter['money']), $user->id, __('购买') . __('漫画') . __('章节') . ',id:' . $chapter['id']);
            return true;
        } else {
            return '自动购买失败';
        }
    }

}