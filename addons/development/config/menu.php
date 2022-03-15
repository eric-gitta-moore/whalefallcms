<?php
/**
 * 菜单配置文件
 */

return [
	    [
	        "type" => "file",
	        "name" => "development/addons",
	        "title" => "插件开发",
	        "icon" => "fa fa-circle-o",
	        "condition" => "",
	        "remark" => "备注",
	        "ismenu" => 1,
	        "sublist" => [
	            [
	                "type" => "file",
	                "name" => "development/auth/rule/index",
	                "title" => "插件菜单",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "development/auth/rule/add",
	                        "title" => "添加",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "development/auth/rule/edit",
	                        "title" => "修改",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "development/auth/rule/import",
	                        "title" => "一键生成",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 1
	                    ]
	                ]
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/add",
	                "title" => "添加新的插件",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/edit",
	                "title" => "编辑",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/package",
	                "title" => "插件打包",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/index",
	                "title" => "列表",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/del",
	                "title" => "删除",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/datatables",
	                "title" => "选择数据表",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/config",
	                "title" => "配置",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ],
	            [
	                "type" => "file",
	                "name" => "development/addons/addconfig",
	                "title" => "添加插件配置",
	                "icon" => "fa fa-circle-o",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 0
	            ]
	        ]
	    ]
	];