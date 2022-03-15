define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'novel/relation/index' + location.search,
                    add_url: 'novel/relation/add',
                    edit_url: 'novel/relation/edit',
                    del_url: 'novel/relation/del',
                    multi_url: 'novel/relation/multi',
                    table: 'novel_relation',
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
                        {field: 'novel_novel_id', title: __('Novel_novel_id')},
                        {field: 'novel_novel_name', title: __('Novel_novel_name')},
                        {field: 'config_cate_id', title: __('Config_cate_id')},
                        {field: 'config_cate_name', title: __('Config_cate_name')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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