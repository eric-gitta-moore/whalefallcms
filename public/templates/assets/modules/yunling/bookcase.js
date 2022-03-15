define(["jquery", "layer", "./common/lazyload.js"],
    function ($, layer, lazyLoad) {
    let Controller = {
        index: function () {
            $(function () {
                //懒加载模块
                lazyLoad();


                $("#edit_btn").on("click", show_edit);
                let select_all_switch = $(".select_all"),
                    select_switch = $("#shelf-container .cp-box .selected-switch:not([hidden])"),
                    selected_selector = "#shelf-container .cp-box .selected-switch.selected",
                    delete_confirm = $(".delete-selected-confirm"),
                    default_size = select_switch.size();

                //显示编辑
                function show_edit() {
                    $("#footer-bar").hide();
                    $("#editable-bar,#shelf-container .cp-box").show();
                    $("#shelf-container").addClass('editable');
                    $("#edit_btn").text("取消");
                    $(this).off('click', show_edit).on('click', hide_edit);
                    select_switch.prop("checked",false);
                    statistics();
                }

                //隐藏编辑
                function hide_edit() {
                    $("#footer-bar").show();
                    $("#editable-bar,#shelf-container .cp-box").hide();
                    $("#shelf-container").removeClass('editable');
                    $("#edit_btn").text("编辑");
                    $(this).off('click', hide_edit).on('click', show_edit);
                    select_switch.prop("checked",false);
                    statistics();
                }

                select_switch.on("click",function () {
                    $(this).toggleClass("selected");
                    statistics();
                    if ($(selected_selector).size() === default_size)
                    {
                        select_all_switch.prop("checked",true);
                    }
                    else
                    {
                        select_all_switch.prop("checked",false);
                    }
                });

                select_all_switch.on("click",function () {
                    console.log(select_all_switch.prop("checked"));
                    if (select_all_switch.prop("checked"))
                    {
                        select_switch.addClass("selected").prop("checked",true);
                    }
                    else
                    {
                        select_switch.removeClass("selected").prop("checked",false);
                    }
                    statistics();
                });

                //删除按钮
                $(".delete-selected-btn").on("click",function () {
                    let size = $("#delete_count").text();
                    size = parseInt(size);
                    if (size <= 0)
                    {
                        layer.msg("您没有选中任何记录",{time:300});
                        return false;
                    }
                    delete_confirm.show();
                });

                //取消删除
                $("#delete-cancel").on("click",function () {
                    delete_confirm.hide();
                });

                //确认删除
                $("#delete-confirm-btn").on("click",function () {
                    let size = $("#delete_count").text();
                    size = parseInt(size);
                    if (size <= 0)
                    {
                        layer.msg("您没有选中任何记录",{time:300});
                        return false;
                    }

                    let that = $(this);

                    if ($(this).hasClass("disabled"))
                    {
                        layer.msg("请求中...",{time:200});
                        return false;
                    }

                    let loading = layer.msg("删除中...");
                    $(this).addClass("disabled");

                    $.post("/api/user/cancelCollectionAll",{
                        data:$(selected_selector).serialize()
                    },function (res) {
                        // $(selected_selector).parents(".item").hide();
                        layer.close(loading);
                        if (res.code === 1)
                        {
                            $(selected_selector).parents(".item").remove();
                            //复原多选框
                            select_switch.prop("checked",false);
                            layer.msg(res.msg,{time:500});
                            delete_confirm.hide();
                        }
                        else
                        {
                            // $(selected_selector).parents(".item").show();
                            layer.msg(res.msg,{time:500});
                        }
                        that.removeClass("disabled");
                        statistics();

                    })

                });
                
                //统计选择个数
                function statistics() {
                    let cnt = $(selected_selector).size();
                    $("#delete_count").text(cnt);
                    $("#confirm-delete-count").text(cnt);
                }



            })
        },
        history:function () {
            $(function () {

                //历史记录
                let his_str = localStorage.getItem("history"),
                    his;
                if (his_str)
                {
                    his = JSON.parse(his_str);
                }
                else
                {
                    his = JSON.parse("{\"data\":[]}");
                }

                // $(".common-ne")


                if (his.data.length > 0)
                {
                    let append_html = "<div class=\"row-list\" id=\"shelf-container\" data-scroll=\"true\">";
                    $.each(his.data,function (i,val) {
                        let img_url = val.cover_image;
                        if (val.cover_image.indexOf("http") !== 0)
                        {
                            img_url = Config.__CDN__ + val.cover_image;
                        }
                        append_html += "<div class=\"item\" data-looking=\"0\">\n" +
                            "            <a href=\"/index/" + val.module +".book/detail/id/" + val.id + "\">\n" +
                            "                <div class=\"cover\">\n" +
                            "                    <img class=\"bookcase lazy\" src=\"" + img_url + "\" alt=\"" + val.name + "\" title=\"" + val.name + "\" />\n" +
                            "                </div>\n" +
                            "                <div class=\"body\">\n" +
                            "                    <div class=\"title\">" + val.name + "</div>\n" +
                            "                    <div class=\"text\">" + val.last_chapter + "</div>\n" +
                            "                </div>\n" +
                            "            </a>\n" +
                            "            <div class=\"cp-box\" style=\"display:none;\">\n" +
                            "                <input type=\"checkbox\" name=\"" + val.id + "\" class=\"selected-switch\" value=\"" + val.module +"\">\n" +
                            "                <div class=\"swtich\"></div>\n" +
                            "            </div>\n" +
                            "        </div>";

                    });
                    append_html += "</div>";

                    $(".common-ne").parents(".bs-box").html(append_html);
                }



            })

        }
    };

    return Controller;

});