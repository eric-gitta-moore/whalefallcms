define(["jquery", "layer"], function ($, layer) {
    let reward = function (type = "cartoon") {
        //打赏
        $("#btn_reward").on("click", function (e) {
            $("#ds").fadeIn(300);
        });
        $("#btn_mask").on("click", function (e) {
            $("#ds").fadeOut(300);
        });
        $("#cancel_redraw").on("click", function (e) {
            $("#ds").fadeOut(300);
        });


        //打赏记录
        let page = 1;
        loadMore(page);
        $("#more_reward").on('click', function () {
            page++;
            loadMore(page);
        });

        /**
         * 异步请求打赏记录
         * @param page
         * @param clear
         */
        function loadMore(page, clear = false) {
            $.get("/api/reward/get/book_id/" + $("#more_reward").attr("data-book_id") + "/type/" + type + "/page/" + page, {}, function (data) {
                // console.log(data);
                let res = data.data.data;
                if (res.length > 0) {
                    let html = "";
                    $(res).each(function (i, v) {
                        html += '<div class="row">'
                            + '<div>'
                            + '<img class="avatar" src="' + v.user.avatar + '" alt="' + v.user.nickname + '">'
                            + '</div>'
                            + '<div class="detail">'
                            + '<p class="nickname">' + v.user.nickname + '</p>'
                            + '<div class="gift">赠送: <img src="' + v.image + '" alt=""> × 1 个礼物给作者</div>'
                            + '<div class="datetime">' + v.create_time_text + '</div>'
                            + '</div>'
                            + '</div>';
                    });
                    if (clear) {
                        $('.tip-history .rows').html(html);
                    } else {
                        $('.tip-history .rows').append(html);
                    }
                } else {
                    // let html = "<div class=\"rows\"><div class=\"row\">没有更多了...<div></div>";
                    // $('.tip-history .rows').append(html);
                    // layer.msg("已经加载到底了");
                    $("#more_reward").hide();
                    $("#nomore").show();
                }
            });
        }

        //监听打赏按钮
        $("#ds li").on("click", function () {
            let sid = $(this).attr("_sid");
            layer.confirm('确定要打赏么？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                let tips = layer.msg('请求中...');
                $.ajax({
                    url:"/api/reward/doReward/score/" + sid + "/book_id/" + $("#more_reward").attr("data-book_id") + "/type/" + type,
                    complete:function (data) {
                        data = data.responseJSON;

                        layer.close(tips);
                        layer.msg(data.msg);
                        loadMore(1, true);
                        $("#btn_mask").click();
                    }
                })
                // $.get("/api/reward/doReward/score/" + sid + "/book_id/" + $("#more_reward").attr("data-book_id") + "/type/" + type, {}, function (data) {
                //     layer.close(tips);
                //     layer.msg(data.msg);
                //     loadMore(1, true);
                //     $("#btn_mask").click();
                // })
            }, function () {
                layer.msg("取消打赏");
            });
        });
    };

    return reward;

});