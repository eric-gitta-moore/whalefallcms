define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'comment/report/index',
                    add_url: 'comment/report/add',
                    edit_url: 'comment/report/edit',
                    del_url: 'comment/report/del',
                    multi_url: 'comment/report/multi',
                    table: 'comment_report',
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
                        {field: 'user.nickname', title: __('Nickname')},
                        {field: 'site_id', title: __('Site_id'), formatter: Table.api.formatter.search},
                        {field: 'site.title', title: __('Site')},
                        {field: 'article_id', title: __('Article_id'), formatter: Table.api.formatter.search},
                        {field: 'article.title', title: __('Article')},
                        {field: 'post_id', title: __('Post_id'), formatter: Table.api.formatter.search},
                        {field: 'post.content', title: __('Post')},
                        {field: 'type', title: __('Type'), formatter: Table.api.formatter.search},
                        {field: 'type_text', title: __('Type')},
                        {field: 'content', title: __('Content')},
                        {field: 'ip', title: __('Ip')},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"settled": __('Settled'), "unsettled": __('Unsettled')}, formatter: Table.api.formatter.status},
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