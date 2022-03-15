<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/


namespace app\service;


use app\common\model\cartoon\Cartoon;
use app\common\model\user\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Model;
use think\Paginator;

class CollectionService
{

    /**
     * 获取用户收藏
     * @param int $user_id
     * @param bool $type
     * @param bool $with_book_detail 是否带上书本详细
     * @param bool $page
     * @param int $list_rows
     * @param array $paginate_config
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws Exception
     * @throws ModelNotFoundException
     */
    public static function getUserCollection($user_id, $type = false, $with_book_detail = false,$with_cate=false,$page=false,$list_rows=10,$paginate_config=[])
    {
        $where = [
            'user_id' => $user_id,
        ];
        if ($type != false && $type = check_type($type))
            $where['status'] = $type;

        $collect = Collection::where($where)->order('weigh');
        if ($page !== false)
        {
            $collect = $collect -> paginate($list_rows,false,$paginate_config);
        }
        else
        {
            $collect = $collect -> select();
        }

        if (!$with_book_detail)
        {
            if ($collect instanceof Paginator)
            {
                return $collect -> toArray();
            }
            else
            {
                return \collection($collect) -> toArray();
            }
        }
        elseif ($collect instanceof Paginator)
        {
            $is_paginator = true;
            $page_other = $collect -> toArray();
            $collect = $collect -> toArray()['data'];
        }

//        halt($collect -> toArray());

        $collect_out = [];

        $group = get_types_key();
        foreach ($collect as $item) {
            $group[$item['status']][] = $item['bookid'];
            $collect_out[$item['bookid']] = $item;
        }

//        trace($group);
//        trace($collect);
//        halt($collect);

//        $book_group = [];
        foreach ($group as $type => $book_ids) {
            if (empty($book_ids))
                continue;
            $book_model = '\app\common\model\\' . $type . '\\' . ucfirst($type);
            $books = $book_model::where('id', 'in', $book_ids)->select();
            foreach ($books as $book) {
                $collect_out[$book['id']]['book_detail'] = $book;
            }
//            array_push($book_group,$books);
        }

        if (isset($is_paginator))
        {
//            $collect = $collect -> toArray();
            $page_other['data'] = $collect_out;
            $collect_out = $page_other;
//            $collect_out = $collect -> getData();
        }

        return $collect_out;


    }

}