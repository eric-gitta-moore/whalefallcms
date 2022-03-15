define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'message/index' + location.search,
                    add_url: 'message/add',
                    edit_url: 'message/edit',
                    del_url: 'message/del',
                    multi_url: 'message/multi',
                    table: 'message'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'message_id',
                sortName: 'message_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'message_id', title: __('Message_id')},
                        {field: 'message_type', title: __('Message_type'), searchList: {"system":__('System'),"user":__('User')}, formatter: Table.api.formatter.normal},
                        {field: 'message_title', title: __('Message_title')},
                        {field: 'createtime', title: __('Createtime'),sortable: true, operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'),sortable: true, operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.showUser();
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.showUser();
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            showUser:function(){
                $('body').on('change', '[data-show-user]', function () {
                    if($(this).val() == 'user'){
                        $("#selectpage_user").append('<input id="c-user_id" placeholder="请选择会员" data-rule="required" data-source="user/user/index" data-field="nickname" class="form-control selectpage" name="row[user_id]" type="text" value="">');
                        Form.events.selectpage($("#selectpage_user"));
                    }else{
                        $("#selectpage_user").html('');
                    }
                });
            }
        }
    };
    return Controller;
});