<?php
/**
 * 菜单配置文件
 */

return [
	    [
	        "type" => "file",
	        "name" => "agent",
	        "title" => "代理管理",
	        "icon" => "fa fa-list",
	        "condition" => "",
	        "remark" => "",
	        "ismenu" => 1,
	        "sublist" => [
	            [
	                "type" => "file",
	                "name" => "agent/distribution",
	                "title" => "分销设置",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "agent/distribution/index",
	                        "title" => "查看",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/distribution/add",
	                        "title" => "添加",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/distribution/edit",
	                        "title" => "编辑",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/distribution/del",
	                        "title" => "删除",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/distribution/multi",
	                        "title" => "批量更新",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ]
	                ]
	            ],
	            [
	                "type" => "file",
	                "name" => "agent/profit",
	                "title" => "代理分润管理",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/index",
	                        "title" => "查看",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/recyclebin",
	                        "title" => "回收站",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/add",
	                        "title" => "添加",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/edit",
	                        "title" => "编辑",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/del",
	                        "title" => "删除",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/destroy",
	                        "title" => "真实删除",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/restore",
	                        "title" => "还原",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "agent/profit/multi",
	                        "title" => "批量更新",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ]
	                ]
	            ]
	        ]
	    ]
	];