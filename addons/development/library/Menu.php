<?php

namespace addons\development\library;

use app\admin\model\AuthRule;
use fast\Tree;
use think\Exception;
use think\exception\PDOException;

class Menu extends \app\common\library\Menu
{



    /**
     * 导出指定名称的菜单规则
     * @param string $name
     * @return array
     */
    public static function export($name)
    {
        $ids = self::getAuthRuleIdsByName($name);

        if (!$ids) {
            return [];
        }
        $menuList = [];
        $menu = AuthRule::getByName($name);
        $ruleList = collection(AuthRule::where('id', 'in', $ids)->select())->toArray();
        $menuList = Tree::instance()->init($ruleList)->getTreeArray(0);
        return $menuList;
    }



}
