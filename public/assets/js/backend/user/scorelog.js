define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/scorelog/index',
                    add_url: 'user/scorelog/add',
                    edit_url: 'user/scorelog/edit',
                    del_url: 'user/scorelog/del',
                    multi_url: 'user/scorelog/multi',
                    table: 'user_score_log',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search},
                        {field: 'score', title: __('Score'), formatter: Table.api.formatter.search},
                        {field: 'before', title: __('Before'), formatter: Table.api.formatter.search},
                        {field: 'after', title: __('After'), formatter: Table.api.formatter.search},
                        {field: 'memo', title: __('Memo'), formatter: Table.api.formatter.search},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $(document).on("change", "select[name=template]", function () {
                $("#c-score").val($(this).val());
                $("#c-memo").val($("option:selected", this).data("memo"));
            });
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