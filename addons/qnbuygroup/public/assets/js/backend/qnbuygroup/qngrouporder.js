define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'qnbuygroup/qngrouporder/index' + location.search,
                    add_url: 'qnbuygroup/qngrouporder/add',
                    edit_url: 'qnbuygroup/qngrouporder/edit',
                    del_url: 'qnbuygroup/qngrouporder/del',
                    multi_url: 'qnbuygroup/qngrouporder/multi',
                    table: 'qnbuygroup_order',
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
                        {field: 'id', title: __('Id'),visible:false},
                        {field: 'orderid', title: __('Orderid')},
                        {field: 'user.username', title: __('User_id')},
                        {field: 'group.groupname', title: __('Group_id')},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'payamount', title: __('Payamount'), operate:'BETWEEN'},
                        {field: 'paytype', title: __('Paytype')},
                        {field: 'paytime', title: __('Paytime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'ip', title: __('Ip')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"created":__('未支付'),"paid":__('已支付'),"expired":__('已过期')}, formatter: Table.api.formatter.status}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        // edit: function () {
        //     Controller.api.bindevent();
        // },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});