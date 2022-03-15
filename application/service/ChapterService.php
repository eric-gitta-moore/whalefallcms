<?php
/**
 * @Author Curtis - 云凌工作室
 * @Website http://www.whalefallcms.com
 * @DateTime 2020/4/10 下午 01:17
 */


namespace app\service;


class ChapterService
{

    public static function getChapterNamespace($type)
    {
        $namespace = 'app\common\model\\' . $type . '\Chapter';
        return $namespace;
    }

}