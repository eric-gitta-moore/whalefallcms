define(["jquery"],function ($) {
    let Controller = {
        index:function () {

            $("#shrink").on("click",function () {
                let flag_ele = $(this).find("a"),
                    collect_box = $(".collect-box"),
                    open_box = $(".open-box");


                if (flag_ele.hasClass("opened"))
                {
                    //已经展开
                    collect_box.show();
                    open_box.hide();
                    flag_ele.removeClass("opened");
                    flag_ele.find("span").text("展开");
                }
                else
                {
                    //已经收缩
                    collect_box.hide();
                    open_box.show();
                    flag_ele.addClass("opened");
                    flag_ele.find("span").text("收起");
                }

            })

        }
    };

    return Controller;
});