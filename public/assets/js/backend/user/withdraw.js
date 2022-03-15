define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/withdraw/index' + location.search,
                    add_url: 'user/withdraw/add',
                    edit_url: 'user/withdraw/edit',
                    del_url: 'user/withdraw/del',
                    multi_url: 'user/withdraw/multi',
                    table: 'withdraw',
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
                        {field: 'user.nickname', title: __('Nickname'), operate: false},
                        {field: 'money', title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search},
                        {field: 'handingfee', title: __('Handingfee'), operate: 'BETWEEN', formatter: Table.api.formatter.search},
                        {field: 'taxes', title: __('Taxes'), operate: 'BETWEEN', formatter: Table.api.formatter.search},
                        {field: 'type', title: __('Type')},
                        {field: 'account', title: __('Account'), formatter: Table.api.formatter.search},
                        {field: 'orderid', title: __('Orderid')},
                        {field: 'transactionid', title: __('Transactionid')},
                        {field: 'memo', title: __('Memo')},
                        {field: 'status', title: __('Status'), searchList: {"created": __('Status created'), "successed": __('Status successed'), "rejected": __('Status rejected')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: false},
                        {field: 'transfertime', title: __('Transfertime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime},
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
                $("form[role=form]").data("validator-options", {
                    rules: {
                        settledmoney: function (element) {
                            var money = parseFloat($("#c-money").val()).toFixed(2);
                            var handingfee = parseFloat($("#c-handingfee").val()).toFixed(2);
                            var taxes = parseFloat($("#c-taxes").val()).toFixed(2);
                            var settledmoney = (money - handingfee - taxes).toFixed(2);
                            return settledmoney > 0 ||
                                '金额输入不正确';
                        },
                        account: function (element) {
                            return this.test(element, "mobile") === true ||
                                this.test(element, "email") === true ||
                                '请填写手机号或者邮箱';
                        }
                    }
                });
                $("#c-handingfee,#c-taxes,#c-money").on("keyup change", function () {
                    var money = parseFloat($("#c-money").val()).toFixed(2);
                    var handingfee = parseFloat($("#c-handingfee").val()).toFixed(2);
                    var taxes = parseFloat($("#c-taxes").val()).toFixed(2);
                    var settledmoney = (money - handingfee - taxes).toFixed(2);
                    $("#c-settledmoney").text("￥" + settledmoney);
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});