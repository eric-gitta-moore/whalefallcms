define(["jquery", "lazyload", "layer", "./common/reward.js", "./common/collection.js", "./common/lazyload.js", "./common/history.js","honeySwitch"],
    function ($, undefined, layer, reward, collection, lazyLoad,honeySwitch) {
        let Controller = {
            show: function (type = "cartoon") {

                //加载打赏模块
                reward(type);

                //懒加载模块
                lazyLoad();


                //收藏模块
                let noCollection = "<a id=\"add_collection\" class=\"shelf rbga0000\"><i class=\"icon-fav\"></i>收藏</a>";
                let hasCollection = "<a id=\"cancel_collection\" class=\"shelf rbga0000 active\"><i class=\"icon-fav\"></i>已收藏</a>";
                let addCollectionSelector = "#add_collection";
                let cancelCollectionSelector = "#cancel_collection";
                let showCollectionListSelector = "#showcollect";
                collection(book_id, type, noCollection, hasCollection, addCollectionSelector, cancelCollectionSelector, showCollectionListSelector);


                //点赞
                $("#showcoll").on("click", function () {
                    let ele = $("#showcoll > a");
                    let ele_class = ele.attr("class");
                    let action;
                    let ele_likes_num = $("#showlikesnum");
                    if (ele_class.indexOf("active") === -1)
                        action = "addLike";
                    else
                        action = "cancelLike";

                    $.get("/api/user/" + action + "/id/" + book_id + "/type/" + type, {}, function (res) {
                        if (action === "addLike") {
                            ele.addClass("active");
                            ele_likes_num.text(parseInt(ele_likes_num.text()) + 1);
                            if (res.code === 1) {
                                layer.msg(res.msg, {time: 500});
                            } else {
                                ele.removeClass("active");
                                ele_likes_num.text(parseInt(ele_likes_num.text()) - 1);
                                layer.msg(res.msg, {time: 500});
                            }
                        } else {
                            ele_likes_num.text(parseInt(ele_likes_num.text()) - 1);
                            ele.removeClass("active");
                            if (res.code === 1) {
                                layer.msg(res.msg, {time: 500});
                            } else {
                                ele.addClass("active");
                                ele_likes_num.text(parseInt(ele_likes_num.text()) + 1);
                                layer.msg(res.msg, {time: 500});
                            }
                        }
                    });
                });

                //上下一章节
                $("#previous,#next").on("click",function () {
                    if ($(this).attr("data-uri") == "")
                    {
                        layer.msg("没有" + $(this).text() + "了",{time:500});
                    }
                    else
                    {
                        window.location.href = $(this).attr("data-uri");
                    }

                });

                //购买章节
                function switchAutoPay(status = 1)
                {
                    $.ajax({
                        url:"/api/user/switchAutoPay",
                        data:{
                            "status":status
                        },
                        complete:function (data) {
                            data = data.responseJSON;
                            layer.msg(data.msg,{time:1000});
                        }
                    })
                }
                $("#auto_pay_btn").on("click",function () {
                    if ($(this).hasClass("switch-off"))
                    {
                        switchAutoPay(0);
                    }
                    else
                    {
                        switchAutoPay(1);
                    }
                });

                $("#buy_btn").on("click",function () {
                    $.ajax({
                        url:"/api/user/buyChapter",
                        data:{
                            "id":chapter_id,
                            "type":type
                        },
                        complete:function (data) {
                            data = data.responseJSON;
                            layer.msg(data.msg,{time:1000});
                            if (data.code === 1)
                            {
                                window.location.reload();
                            }
                        }
                    })
                });
                $("#share_btn").on("click",function () {
                    layer.open({
                        type: 2,
                        title: '推广返利！！！',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%', '90%'],
                        content: '/index/invite/index' //iframe的url
                    });
                });
                $("#open_vip_btn").on("click",function () {
                    layer.open({
                        type: 2,
                        title: '开通VIP全场免费享',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%', '90%'],
                        content: '/index/qnbuygroup.buygroup/buy' //iframe的url
                    });
                });
                $("#my_score_btn").on("click",function () {
                    layer.open({
                        type: 2,
                        title: '充值',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['95%', '90%'],
                        content: '/index/recharge/recharge' //iframe的url
                    });
                });

                if (is_show_tips && tips_msg.length !== 0)
                {
                    layer.msg(tips_msg);
                }


            }
        };

        return Controller;

    });