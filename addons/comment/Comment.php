<?php

namespace addons\comment;

use app\common\library\Menu;
use think\Addons;

/**
 * 评论插件
 */
class Comment extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
                'name'    => 'comment',
                'title'   => '评论管理',
                "icon"    => "fa fa-comments",
                "ismenu"  => 1,
                'sublist' => [
                    [
                        "name"    => "comment/post",
                        "title"   => "评论管理",
                        "icon"    => "fa fa-comment",
                        "ismenu"  => 1,
                        "remark"  => "管理所有会员发布的评论",
                        "sublist" => [
                            [
                                "name"  => "comment/post/index",
                                "title" => "查看"
                            ],
                            [
                                "name"  => "comment/post/add",
                                "title" => "添加"
                            ],
                            [
                                "name"  => "comment/post/edit",
                                "title" => "编辑"
                            ],
                            [
                                "name"  => "comment/post/del",
                                "title" => "删除"
                            ],
                            [
                                "name"  => "comment/post/multi",
                                "title" => "批量更新"
                            ]
                        ]
                    ],
                    [
                        "name"    => "comment/site",
                        "title"   => "站点管理",
                        "icon"    => "fa fa-list",
                        "ismenu"  => 1,
                        "remark"  => "",
                        "sublist" => [
                            [
                                "name"  => "comment/site/index",
                                "title" => "查看"
                            ],
                            [
                                "name"  => "comment/site/add",
                                "title" => "添加"
                            ],
                            [
                                "name"  => "comment/site/edit",
                                "title" => "编辑"
                            ],
                            [
                                "name"  => "comment/site/del",
                                "title" => "删除"
                            ],
                            [
                                "name"  => "comment/site/multi",
                                "title" => "批量更新"
                            ]
                        ]
                    ],
                    [
                        "name"    => "comment/article",
                        "title"   => "文章管理",
                        "icon"    => "fa fa-circle",
                        "ismenu"  => 1,
                        "remark"  => "",
                        "sublist" => [
                            [
                                "name"  => "comment/article/index",
                                "title" => "查看"
                            ],
                            [
                                "name"  => "comment/article/add",
                                "title" => "添加"
                            ],
                            [
                                "name"  => "comment/article/edit",
                                "title" => "编辑"
                            ],
                            [
                                "name"  => "comment/article/del",
                                "title" => "删除"
                            ],
                            [
                                "name"  => "comment/article/multi",
                                "title" => "批量更新"
                            ]
                        ]
                    ],
                    [
                        "name"    => "comment/report",
                        "title"   => "举报管理",
                        "icon"    => "fa fa-circle",
                        "ismenu"  => 1,
                        "remark"  => "",
                        "sublist" => [
                            [
                                "name"  => "comment/report/index",
                                "title" => "查看"
                            ],
                            [
                                "name"  => "comment/report/add",
                                "title" => "添加"
                            ],
                            [
                                "name"  => "comment/report/edit",
                                "title" => "编辑"
                            ],
                            [
                                "name"  => "comment/report/del",
                                "title" => "删除"
                            ],
                            [
                                "name"  => "comment/report/multi",
                                "title" => "批量更新"
                            ]
                        ]
                    ],
                    [
                        "name"    => "comment/statistics",
                        "title"   => "统计管理",
                        "icon"    => "fa fa-bar-chart",
                        "ismenu"  => 1,
                        "sublist" => [
                            [
                                "name"  => "comment/statistics/index",
                                "title" => "查看"
                            ]
                        ]
                    ]
                ],
            ]
        ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('comment');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable("comment");
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {

        Menu::disable("comment");
        return true;
    }

}
