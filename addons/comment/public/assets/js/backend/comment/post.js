define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'comment/post/index',
                    add_url: 'comment/post/add',
                    edit_url: 'comment/post/edit',
                    del_url: 'comment/post/del',
                    multi_url: 'comment/post/multi',
                    table: 'comment_post',
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
                        {field: 'article_id', title: __('Article_id'), formatter: Table.api.formatter.search},
                        {field: 'article.title', title: __('Article')},
                        {field: 'pid', title: __('Pid'), formatter: Table.api.formatter.search},
                        {field: 'site_id', title: __('Site_id'), formatter: Table.api.formatter.search},
                        {field: 'site.title', title: __('Site')},
                        {field: 'content', title: __('Content')},
                        {field: 'ip', title: __('Ip'), formatter: Table.api.formatter.search},
                        {field: 'comments', title: __('Comments')},
                        {field: 'likes', title: __('Likes')},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', visible: false, title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'deletetime', title: __('Deletetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"normal": __('Normal'), "hidden": __('Hidden'), "deleted": __('Deleted')}, formatter: Table.api.formatter.status},
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