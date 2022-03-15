define(["jquery", "lazyload", "layer", 'mescroll','./common/auto_task.js','honeySwitch'],
    function ($, undefined, layer, MeScroll) {
        let Controller = {
            show: function (type = "cartoon") {
                //自动购买开关
                if (auto_pay)
                {
                    honeySwitch.showOn('#auto_pay_btn');
                }
                else
                {
                    honeySwitch.showOff('#auto_pay_btn');
                }

                //保存观看高度
                $(window).scroll(function () {
                    if($(document).scrollTop()!=0){
                        sessionStorage.setItem("offsetTop", $(window).scrollTop());
                    }
                });

                //恢复之前的高度
                let offset = sessionStorage.getItem("offsetTop");
                $(document).scrollTop(offset);


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
                    });
                }
                switchEvent('#auto_pay_btn',function () {
                    switchAutoPay(1)
                },function () {
                    switchAutoPay(0)
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

                function show_tools() {
                    if ($('.navtop').css("top") == "-50px") {
                        $('.navtop').css('top', '0px');
                        $(".control_bottom").css('bottom', '0px');
                    } else {
                        $('.navtop').css('top', '-50px');
                        $(".control_bottom").css('bottom', '-70px');
                    }
                }
                $('.imgbg,.mask,#mescroll').on('click',show_tools);
                $('a.stop').click(function () {
                    $('html,body').animate({scrollTop: '0px'}, 300);
                    $('.navtop').css('top', '-50px');
                    $(".control_bottom").css('bottom', '-70px');
                });
                $('a.sbottom').click(function () {
                    $('html,body').animate({scrollTop: $('.bottom').offset().top}, 300);
                });
                $("#addFavBTN").click(function () {
                    $("#addFavBox").addClass("is-visible");
                });
                $("#addFavBox a").click(function () {
                    $("#addFavBox").removeClass("is-visible");
                });
                $(".needPay a.ticlose").click(function () {
                    $("#needPay").removeClass("is-visible");
                    $(".mask").hide();
                });
                $('body').on('click', '#open', function () {
                    $('.body').on('touchmove', function (event) {
                        event.preventDefault()
                    });
                    $('.left-nav').css('left', '0px');
                    $('.openbg').show();
                });
                $(".openbg,a.close").click(function () {
                    $('.body').off('touchmove');
                    $('.left-nav').css('left', '-280px');
                    $('.openbg').hide();
                });

                function setCookie(name, value) {
                    var Days = 30;
                    var exp = new Date();
                    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
                    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
                }

                function getCookie(name) {
                    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
                    if (arr = document.cookie.match(reg))
                        return unescape(arr[2]);
                    else
                        return null;
                }


                //图片加载
                imageloading();
                function imageloading() {
                    $.ajax({
                        url: "/api/" +  module + ".chapter/get/id/" + chapter_id,
                        type: "get",
                        dataType: "json",
                        async: true,
                        success: function (res) {
                            console.log(res);
                            let appendImg = "";
                            let tpl = $("#litemppic").html();
                            $.each(res.data.photos, function (index, photo) {
                                let img = (photo.image!=null?photo.image:photo.pic_url);
                                console.log(img);
                                appendImg += tpl.replace(/{img}/g, img);
                            });
                            if (res.data.info.is_show_tips)
                            {
                                layer.msg(res.data.info.tips_msg);
                            }
                            if (res.data.info.payed === true)
                            {
                                $('.yunling-reader-chapter-order').hide();
                            }
                            // 1 == f && myTips("成功购买章节");
                            // 0 == e && ($(".showimg").addClass("height"), $("#needPay").addClass("is-visible"), $(".mask").show());
                            if (res.data.info.append_html)
                            {
                                appendImg += res.data.info.append_html;
                            }
                            //插入图集
                            $("#showimgcontent").after(appendImg);

                            ///延迟加载
                            $("img.lazy").lazyload({
                                placeholder: "/templates/assets/modules/default/static/images/loadimg.gif",
                                effect: "fadeIn",
                                threshold: 200
                            });
                        }
                    });
                }

                //侧边列表
                sidelink();
                console.log(chapter_id);
                function sidelink() {
                    $.ajax({
                        url: "/api/" + module + '.book/getBookChapter/id/' + book_info.id,
                        type: "get",
                        dataType: "json",
                        async: true,
                        success: function (res) {
                            let appendHtml = "",data = res.data;
                            let tpl = $("#litemptxt").html();
                            $.each(data, function (index, value) {
                                appendHtml += tpl.replace(/{name}/g, value.name)
                                    .replace(/{nlink}/g, '/index/' + module + '.chapter/show/id/' + value.id)
                                    .replace(/{id}/g, value.id);
                                if (chapter_id === value.id)
                                {
                                    $("#open span").text(index + 1);
                                }
                            });
                            $("#num").text(data.length);
                            $("#listtext").after(appendHtml)
                        }
                    })
                }

                //错误反馈
                feedback();
                function feedback() {
                    $("#rbtn").click(function (l, k) {
                        $("#outwin").addClass("is-visible");
                        k = $(this).attr("data-url");
                    });

                    $(".submit-button").click(function () {
                        var c = $("input[name='title']:checked").val(),
                            h = $("#errortext").val();
                        $.ajax({
                            type: "POST",
                            url: "/e/extend/feedback.php?",
                            data: "enews=AddFeedback&bid=1&ajax=1&url=" + k + "&title=" + c + "&saytext=" + h,
                            success: function (c, h) {
                                "200" == c ? (myTips("提交成功，感谢您的支持！"), $(".form-text").fadeOut(), $("#outwin").removeClass("is-visible"), $("#errortext").val("")) : myTips("系统错误，提交失败！")
                            }
                        });
                        return !1
                    });
                    $(".form-line input").click(function () {
                        $(".form-text").fadeOut()
                    });
                    $(".form-line input.other").click(function () {
                        $(".form-text").fadeIn();
                        console.log('aaaa');
                    });
                    $(".outwin-title a,.cancel-button").click(function () {
                        $(".form-text").fadeOut();
                        $("#outwin").removeClass("is-visible")
                    })
                }

                //左右键翻页
                $(document).on("keydown", function (event) {
                    switch (event.keyCode) {
                        case 37:
                            window.location.href = $('#pre').attr('href');
                            break;
                        case 39:
                            window.location.href = $('#next').attr('href');
                            break;
                    }
                });

                //上下一章节
                let previous_url = $('#pre').attr('data-uri'),
                    next_url = $('#next').attr('data-uri');
                $("a.prev,#pre").on('click',function () {
                    if (previous_url)
                    {
                        window.location.href = previous_url;
                    }
                    else
                    {
                        layer.msg('没有上一章节了',{time:1000});
                    }

                });
                $("a.next,#next").on('click',function () {
                    if (next_url)
                    {
                        window.location.href = next_url;
                    }
                    else
                    {
                        layer.msg('没有下一章节了',{time:1000});
                    }

                });

            }
        };

        return Controller;

    });