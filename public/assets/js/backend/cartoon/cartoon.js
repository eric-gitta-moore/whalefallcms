define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cartoon/cartoon/index' + location.search,
                    add_url: 'cartoon/cartoon/add',
                    edit_url: 'cartoon/cartoon/edit',
                    del_url: 'cartoon/cartoon/del',
                    multi_url: 'cartoon/cartoon/multi',
                    table: 'cartoon',
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
                        {field: 'id', title: __('Id'),sortable:true,width: "84px"},
                        {field: 'name', title: __('Name')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,sortable:true},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,sortable:true},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status,sortable:true},
                        {field: 'cover_image', title: __('Cover_image'), events: Table.api.events.image, formatter: Table.api.formatter.image,width:"120px"},
                        {field: 'horizon_image', title: __('Horizon_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'big_image', title: __('Big_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'log_time', title: __('Log_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'cartoon_author_id', title: __('Cartoon_author_id'),sortable:true,width:"110px"},
                        {field: 'switch', title: __('Switch'), searchList: {"0":__('Switch 0'),"1":__('Switch 1'),"2":__('Switch 2'),"3":__('Switch 3')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'free_type', title: __('收费'), searchList: {"0":__('Free_type 0'),"1":__('Free_type 1')}, formatter: Table.api.formatter.toggle},
                        {field: 'vip_type', title: __('Vip_type'), searchList: {"0":__('Vip_type 0'),"1":__('Vip_type 1')}, formatter: Table.api.formatter.toggle},
                        {field: 'last_chapter', title: __('Last_chapter')},
                        {field: 'start_pay', title: __('Start_pay'),sortable:true,width:"150px"},
                        {field: 'readnum', title: __('Readnum'),sortable:true,width:"80px"},
                        {field: 'collectnum', title: __('Collectnum'),sortable:true,width:"80px"},
                        {field: 'likenum', title: __('Likenum'),sortable:true,width:"80px"},
                        {field: 'new_switch', title: __('New_switch'), searchList: {"0":__('New_switch 0'),"1":__('New_switch 1')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'recommend_switch', title: __('Recommend_switch'), searchList: {"0":__('Recommend_switch 0'),"1":__('Recommend_switch 1')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'chargenum', title: __('Chargenum'),sortable:true,width:"80px"},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('更多'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: __('章节管理'),
                                    title: __('章节管理'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-dialog',
                                    icon: 'fa fa-magic',
                                    url: 'cartoon/chapter/index/cartoon_cartoon_id/{id}',
                                },
                            ],
                            formatter: Table.api.formatter.buttons
                        },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'cartoon/cartoon/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'cartoon/cartoon/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'cartoon/cartoon/destroy',
                                    refresh: true
                                }
                            ],
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
        batch: function () {
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