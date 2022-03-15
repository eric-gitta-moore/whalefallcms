define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/level/index',
                    add_url: 'user/level/add',
                    edit_url: 'user/level/edit',
                    del_url: 'user/level/del',
                    multi_url: 'user/level/multi',
                    table: 'user_level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'level_id',
                sortName: 'level_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'level_id', title: __('Level_id')},
                        {
                            field: 'level_img',
                            title: __('level_img'),
                            operate: false,
                            formatter: Table.api.formatter.image
                        },
                        {field: 'level_name', title: __('Level_name')},
                        {field: 'amount', title: __('Amount'), operate: 'BETWEEN'},
                        {field: 'discount', title: __('Discount')},
                        {field: 'describe', title: __('Describe')},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});