define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'addon/downloaded',
                    add_url: 'development/addons/add',
                    edit_url: '',
                    del_url: '',
                }
            });

            var table = $("#table");
            var tableOptions = {
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                escape: false,
                pk: 'id',
                sortName: 'weigh',
                pagination: false,
                commonSearch: false,
                search: true,
                templateView: false,
                clickToSelect: false,
                showColumns: false,
                showToggle: false,
                showExport: false,
                showSearch: false,
                searchFormVisible: true,
                columns: [
                    [
                        {field: 'id', title: 'ID', operate: false, visible: false},
                        {field: 'home', title: __('Index'), width: '50px', formatter: Controller.api.formatter.home},
                        {field: 'name', title: __('Name'), operate: false, visible: false, width: '120px'},
                        {field: 'title', title: __('插件标题'), operate: 'LIKE', align: 'left'},
                        {field: 'intro', title: __('介绍'), operate: 'LIKE', align: 'left', class: 'visible-lg'},
                        {field: 'author', title: __('开发者'), operate: 'LIKE', width: '100px'},
                        {field: 'downloads', title: __('下载量'), operate: 'LIKE', width: '80px', align: 'center', formatter: Controller.api.formatter.downloads},
                        {field: 'version', title: __('版本'), operate: 'LIKE', width: '80px', align: 'center'},
                        {field: 'operate', title: __('Operate'),
                            buttons: [
                                {
                                    name: 'rule',
                                    text: '菜单',
                                    title: function (row) {
                                        return "["+row.title+"]插件菜单管理";
                                    },
                                    icon: 'fa fa-bars fa-fw',
                                    classname: 'btn btn-xs btn-primary btn-dialog ',
                                    url: function (row) {
                                        return 'development/auth/rule/index?name='+row.name;

                                    }
                                },
                                {
                                    name: 'edit1',
                                    text: '编辑',
                                    title: function (row) {
                                        return "["+row.title+"]插件编辑";
                                    },
                                    icon: 'fa fa-pencil',
                                    classname: 'btn btn-xs btn-success btn-dialog ',
                                    url: function (row) {
                                        return 'development/addons/edit?name='+row.name;
                                    }
                                },
                                {
                                    name: 'config',
                                    text: '配置',
                                    title: function (row) {
                                        return "["+row.title+"]插件配置";
                                    },
                                    icon: 'fa fa-cogs fa-fw',
                                    classname: 'btn btn-xs btn-info btn-dialog ',
                                    url: function (row) {
                                        return 'development/addons/config?name='+row.name;
                                    }
                                },

                                {
                                    name: 'download',
                                    text: '打包',
                                    title: function (row) {
                                        return "["+row.title+"]插件打包";
                                    },
                                    icon: 'fa fa-download',
                                    classname: 'btn btn-xs btn-success btn-dialog ',
                                    url: function (row) {
                                        return 'development/addons/package?name='+row.name;
                                    }
                                }

                            ],
                            table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            };
            // 初始化表格
            table.bootstrapTable(tableOptions);

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('load-success.bs.table',function(data){
                $(".btn-primary").data("area", ["80%","90%"]);
            });
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"),function () {
                var url='addon';
                Backend.api.closetabs(url);//配置缓存问题，暂时找不到刷新的办法，先把他关了
            });
        },
        edit: function () {
            Controller.api.bindevent();
        },
        package: function () {
            Controller.api.bindevent();
        },
        config: function () {
            //更新配置
            Form.api.bindevent($("#edit-form"), function(data, ret){
                Toastr.success("成功");
            }, function(data, ret){
                Toastr.success("失败");
                return false;
            }, function(success, error){
                return true;
            });
            //添加配置
            Form.api.bindevent($("#add-form"), function(data, ret){
                Toastr.success("成功");
                location.reload();
                return false;
            }, function(data, ret){
                Toastr.success("失败");
                return false;
            }, function(success, error){
                return true;
            });
            //删除配置
            $(document).on("click", ".btn-delcfg", function () {
                var that = this;
                Layer.confirm(__('Are you sure you want to delete this item?'), {icon: 3, title:'提示'}, function (index) {
                    $(that).closest("tr").remove();
                    Layer.close(index);
                });

            });
            //切换显示隐藏变量字典列表
            $(document).on("change", "form#add-form select[name='row[type]']", function (e) {
                $("#add-content-container").toggleClass("hide", ['select', 'selects', 'checkbox', 'radio'].indexOf($(this).val()) > -1 ? false : true);
            });
        },
        api: {
            formatter: {
                downloads: function (value, row, index) {
                    return value;
                },
                home: function (value, row, index) {
                    return row.addon ? '<a href="' + row.addon.url + '" data-toggle="tooltip" title="' + __('View addon index page') + '" target="_blank"><i class="fa fa-home text-primary"></i></a>' : '<a href="javascript:;"><i class="fa fa-home text-gray"></i></a>';
                },
            },
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },

        }
    };
    return Controller;
});