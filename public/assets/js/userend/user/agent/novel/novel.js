define(['jquery', 'bootstrap', 'userend', 'table', 'form'], function ($, undefined, Userend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user.agent.novel.novel/index' + location.search,
                    table: 'novel',
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
                        {field: 'name', title: __('Name')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'cover_image', title: __('Cover_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'horizon_image', title: __('Horizon_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'big_image', title: __('Big_image'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'log_time', title: __('Log_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'novel_author_id', title: __('Novel_author_id')},
                        {field: 'switch', title: __('Switch'), searchList: {"0":__('Switch 0'),"1":__('Switch 1'),"2":__('Switch 2'),"3":__('Switch 3')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'free_type', title: __('Free_type'), searchList: {"0":__('Free_type 0'),"1":__('Free_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'vip_type', title: __('Vip_type'), searchList: {"0":__('Vip_type 0'),"1":__('Vip_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'last_chapter', title: __('Last_chapter')},
                        {field: 'start_pay', title: __('Start_pay')},
                        {field: 'readnum', title: __('Readnum')},
                        {field: 'collectnum', title: __('Collectnum')},
                        {field: 'likenum', title: __('Likenum')},
                        {field: 'new_switch', title: __('New_switch'), searchList: {"0":__('New_switch 0'),"1":__('New_switch 1')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'recommend_switch', title: __('Recommend_switch'), searchList: {"0":__('Recommend_switch 0'),"1":__('Recommend_switch 1')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'chargenum', title: __('Chargenum')},
                        {field: 'url', title: __('推广链接'), formatter:function (value, row, index)
                            {
                                let config = requirejs.s.contexts._.config.config;
                                let url = config.site_domain;
                                // console.log(row);
                                // console.log(index);
                                value = url + "index/novel.book/detail/id/" + row.id + "/pid/" + config.user.id;
                                return '<div class="input-group input-group-sm" style="width:250px;margin:0 auto;"><input type="text" class="form-control input-sm" value="' + value + '"><span class="input-group-btn input-group-sm"><a href="' + value + '" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-link"></i></a></span></div>';
                            }
                        },
                        {
                            field: 'buttons',
                            width: "120px",
                            title: __('操作'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: __('章节管理'),
                                    title: __('章节管理'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-dialog',
                                    icon: 'fa fa-magic',
                                    url: 'user.agent.novel.chapter/index/novel_novel_id/{id}',
                                },
                            ],
                            formatter: Table.api.formatter.buttons
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});