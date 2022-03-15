<?php
/**
 * 菜单配置文件
 */

return [
	    [
	        "type" => "file",
	        "name" => "templates",
	        "title" => "模板管理",
	        "icon" => "fa fa-window-restore",
	        "condition" => "",
	        "remark" => "",
	        "ismenu" => 1,
	        "sublist" => [
	            [
	                "type" => "file",
	                "name" => "templates/templates",
	                "title" => "模板列表",
	                "icon" => "fa fa-window-restore",
	                "condition" => "",
	                "remark" => "",
	                "ismenu" => 1,
	                "sublist" => [
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/index",
	                        "title" => "模板列表",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/add",
	                        "title" => "创建模板",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/edit",
	                        "title" => "使用模板",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/reset",
	                        "title" => "重置模板",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/del",
	                        "title" => "删除模板",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/package",
	                        "title" => "模板打包",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/local",
	                        "title" => "上传安装模板",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/config",
	                        "title" => "模板配置",
	                        "icon" => "fa fa-circle-o",
	                        "condition" => "",
	                        "remark" => "",
	                        "ismenu" => 0
	                    ],
	                    [
	                        "type" => "file",
	                        "name" => "templates/templates/addconfig",
	                        "title" => "添加模板配置",
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