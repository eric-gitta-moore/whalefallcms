define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function ($, undefined, Backend, Table, Form, Template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'templates/templates/index',
                    edit_url: 'templates/templates/edit',
                    del_url: 'templates/templates/del',
                }
            });

            var table = $("#table");

            Template.helper("Moment", Moment);

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                templateView: true,
                columns: [
                    [],
                ],
                //禁用默认搜索
                search: false,
                //启用普通表单搜索
                commonSearch: false,
                //可以控制是否默认显示搜索单表,false则隐藏,默认为false
                searchFormVisible: false,
                //分页大小
                pageSize: 12,
                showColumns: false,
                showToggle: false,
                showExport: false,
                showSearch: false,

                pagination: false,
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
            $(".btn-reset").on("click",function(e){
                var that = this;
                Layer.confirm(
                    __('确定一键还原所有模板?'),
                    {icon: 3, title: __('Warning'), shadeClose: true},
                    function (index) {
                        Layer.close(index);
                        var options = $.extend({}, $(that).data() || {});
                        if (typeof options.url === 'undefined' && $(that).attr("href")) {
                            options.url = $(that).attr("href");
                        }
                        options.url = Backend.api.replaceids(this, options.url);
                        var error = typeof options.error === 'function' ? options.error : null;
                        var success = function () {
                            $(".btn-refresh").trigger("click");
                        }
                        if (typeof options.confirm !== 'undefined') {
                            Layer.confirm(options.confirm, function (index) {
                                Backend.api.ajax(options, success, error);
                                Layer.close(index);
                            });
                        } else {
                            Backend.api.ajax(options, success, error);

                        }

                        return false;
                    }
                );
            });

            // 上传模板
            require(['upload'], function (Upload) {
                Upload.api.plupload("#plupload-template", function (data, ret) {
                    Toastr.success(ret.msg);
                    $(".btn-refresh").trigger("click");
                });
            });

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            // 初始化表格参数配置
            var $module_name=$("#module_name").val();
            Controller.api.bindevent();
        },
        package: function () {
            // 初始化表格参数配置
            var $module_name=$("#module_name").val();
            Controller.api.bindevent();
        },
        config: function () {
            //更新配置
            Form.api.bindevent($("#edit-form"), function(data, ret){
                Toastr.success("成功");
            }, function(data, ret){
                Toastr.success("失败");
                return false;
            }, function(success, error){
                return true;
            });
            //添加配置
            Form.api.bindevent($("#add-form"), function(data, ret){
                Toastr.success("成功");
                location.reload();
                return false;
            }, function(data, ret){
                Toastr.success("失败");
                return false;
            }, function(success, error){
                return true;
            });
            //删除配置
            $(document).on("click", ".btn-delcfg", function () {
                var that = this;
                Layer.confirm(__('Are you sure you want to delete this item?'), {icon: 3, title:'提示'}, function (index) {
                    $(that).closest("tr").remove();
                    Layer.close(index);
                });

            });
            //切换显示隐藏变量字典列表
            $(document).on("change", "form#add-form select[name='row[type]']", function (e) {
                $("#add-content-container").toggleClass("hide", ['select', 'selects', 'checkbox', 'radio'].indexOf($(this).val()) > -1 ? false : true);
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                url: function (value, row, index) {
                    return '<div class="input-group input-group-sm" style="width:250px;"><input type="text" class="form-control input-sm" value="' + value + '"><span class="input-group-btn input-group-sm"><a href="' + value + '" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-link"></i></a></span></div>';
                },
                ip: function (value, row, index) {
                    return '<a class="btn btn-xs btn-ip bg-success"><i class="fa fa-map-marker"></i> ' + value + '</a>';
                },
                browser: function (value, row, index) {
                    //这里我们直接使用row的数据
                    return '<a class="btn btn-xs btn-browser">' + row.useragent.split(" ")[0] + '</a>';
                }
            },
            events: {
                ip: {
                    'click .btn-ip': function (e, value, row, index) {
                        var options = $("#table").bootstrapTable('getOptions');
                        //这里我们手动将数据填充到表单然后提交
                        $("#commonSearchContent_" + options.idTable + " form [name='ip']").val(value);
                        $("#commonSearchContent_" + options.idTable + " form").trigger('submit');
                        Toastr.info("执行了自定义搜索操作");
                    }
                },
                browser: {
                    'click .btn-browser': function (e, value, row, index) {
                        Layer.alert("该行数据为: <code>" + JSON.stringify(row) + "</code>");
                    }
                }
            }
        }
    };
    return Controller;
});