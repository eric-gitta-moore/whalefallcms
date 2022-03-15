define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qnbuygroup/qngroupset/index' + location.search,
                    add_url: 'qnbuygroup/qngroupset/add',
                    edit_url: 'qnbuygroup/qngroupset/edit',
                    del_url: 'qnbuygroup/qngroupset/del',
                    multi_url: 'qnbuygroup/qngroupset/multi',
                    table: 'qnbuygroup_set',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), visible: false},
                        {field: 'groupname', title: __('Groupname')},
                        {field: 'group.name', title: __('Group_id')},
                        {
                            field: 'amount',
                            title: __('Amount'),
                            operate: 'BETWEEN'
                        },
                        {field: 'exp', title: __('Exp')},
                        {field: 'weigh', title: __('Weigh')},
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