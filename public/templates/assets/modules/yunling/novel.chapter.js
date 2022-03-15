define(["./chapter.js"], function (chapter) {
    let Controller = {
        show: function () {
            $(function () {

                /**
                 * 保存到本地
                 * @param font_size
                 * @param isNight
                 * @param skin
                 */
                function set_local(font_size=false,isNight=false,skin=false)
                {
                    if (font_size !== false)
                    {
                        localStorage.setItem("font_size",font_size.toString());
                    }
                    if (isNight !== false)
                    {
                        localStorage.setItem("isNight",isNight.toString());
                    }
                    if (skin !== false)
                    {
                        localStorage.setItem("skin",skin.toString());
                    }
                }


                let type = "novel";
                chapter.show(type);


                let fsize = 18,//字体大小
                    isNight = 1,//1日间模式，2夜间模式
                    ps = "";//皮肤
                if (localStorage.getItem("font_size"))
                {
                    fsize = localStorage.getItem("font_size");
                }
                if (localStorage.getItem("isNight"))
                {
                    isNight = localStorage.getItem("isNight");
                }
                if (localStorage.getItem("skin"))
                {
                    ps = localStorage.getItem("skin");
                }
                // let isNight = "{$_GET['isNight']}" ? "{$_GET['isNight']}" : 1;//夜间模式
                // let ps = "{$_GET['ps']}" ? "{$_GET['ps']}" : '';//皮肤

                //夜间模式判断
                if (isNight != "" && isNight == "2") {
                    $('body').removeClass().addClass('night');
                    $("#footer-bar").removeClass().addClass("night tabar flb");
                }
                //皮肤判断
                if (ps != '') {
                    $('.color-match .colors').eq(ps).addClass("act");
                    let ps1 = $('.color-match .colors').eq(ps).attr("class");
                    let ps2 = ps1.split(' ');
                    $('body').removeClass().addClass(ps2[1]);
                    $("#footer-bar").removeClass().addClass(ps2[1] + " tabar flb");
                }

                //监听夜间模式按钮
                $('.tonight .moon').click(function () {
                    $(this).find('i').css("color", "yellow");
                    $(this).find('span').css("color", "yellow");
                    $(this).siblings().find('i').css("color", "#fff");
                    $(this).siblings().find('span').css("color", "#fff");
                    $('body').removeClass().addClass('night');
                    $("#footer-bar").removeClass().addClass("night tabar flb");
                    isNight = 2;
                    ps = '';
                    set_local(false,isNight,ps);
                });

                //监听日间模式按钮
                $('.tonight .sun').click(function () {
                    $(this).find('i').css("color", "yellow");
                    $(this).find('span').css("color", "yellow");
                    $(this).siblings().find('i').css("color", "#fff");
                    $(this).siblings().find('span').css("color", "#fff");
                    $('body').removeClass();
                    $("#footer-bar").removeClass().addClass("tabar flb");
                    isNight = 1;
                    ps = '';
                    set_local(false,isNight,ps);
                });

                //监听皮肤按钮
                $('.color-match .colors').click(function () {
                    // alert($(this).index());
                    $('.color-match .colors').eq($(this).index()).addClass('act').siblings().removeClass('act');
                    var var1 = $(this).attr("class");
                    var var2 = var1.split(' ');
                    // alert(var2[1]);
                    $('body').removeClass().addClass(var2[1]);
                    isNight = 1;
                    ps = $(this).index();
                    set_local(false,isNight,ps);
                });


                $('.item').css('font-size', fsize + 'px');
                $('.zh').text(fsize - 9);

                /**
                 * 显示工具栏
                 */
                function show() {
                    if ($(".tonight").css("display") == "none") {
                        $(".tonight").slideDown("500");
                    } else {
                        $(".tonight").slideUp("500");
                    }
                }

                //监听内容显示区域点击事件
                $('.item').click(function () {
                    if ($("#footer-bar").css("display") == "none")
                        show();
                });
                $('#setting').click(function () {
                    show();
                });


                //增大字体
                $('.ajian').click(function () {
                    $('.ajia').removeClass('jin');
                    if (parseInt($('.zh').text()) <= 5) {
                        $('.ajian').addClass('jin');
                    } else {
                        $('.zh').text(parseInt($('.zh').text()) - 1);
                        $('.item').css('font-size', parseInt($('.zh').text()) + 9 + 'px');
                        if (parseInt($('.zh').text()) <= 5) {
                            $('.ajian').addClass('jin');
                        }
                    }
                    fsize = parseInt($('.zh').text()) + 9;
                    set_local(fsize);
                });

                //减小字体
                $('.ajia').click(function () {
                    $('.ajian').removeClass('jin');
                    if (parseInt($('.zh').text()) >= 15) {
                        $('.ajia').addClass('jin');
                    } else {
                        $('.zh').text(parseInt($('.zh').text()) + 1);
                        $('.item').css('font-size', parseInt($('.zh').text()) + 9 + 'px');
                        if (parseInt($('.zh').text()) >= 15) {
                            $('.ajia').addClass('jin');
                        }
                    }

                    fsize = parseInt($('.zh').text()) + 9;
                    set_local(fsize);

                });

                let winSTbefore = 0;//声明一个变量，用于装触发scroll事件的上一个scrollTop
                function monitor() {
                    let winH = window.innerHeight;    //获取浏览器窗口高度，若要支持IE需要在此处做兼容
                    let winST = $(window).scrollTop();  //获取scrollTop
                    let docH = $(document).height();  //获取文档高度
                    let arr = [winH, winST, docH];
                    return arr;
                }

                monitor();


                $(window).scroll(function () {
                    var arr = monitor();
                    var winH = arr[0];
                    var winST = arr[1];
                    var docH = arr[2];
                    if (winST <= winH / 10) {
                        $('.chapter-menu').hide(); //在首屏时隐藏
                        $('.rt-bar').removeClass('flt');
                        $('.rt-bar').css('position', 'absolute');
                    } else if (winST + winH >= docH) {
                        $('.chapter-menu').hide(); //到达底部时隐藏
                        $('.rt-bar').removeClass('flt');
                        $('.rt-bar').css('position', 'absolute');
                    } else if (winST > winSTbefore) {
                        $('.chapter-menu').hide();    //向下滑动时隐藏
                        $('#footer-bar').hide();    //向下滑动时隐藏
                        $('.rt-bar').removeClass('flt');
                        $('.rt-bar').css('position', 'absolute');
                    } else if (winST < winSTbefore) {
                        $('.chapter-menu').show(); //向上滑动时显示
                        $('#footer-bar').show(); //向上滑动时显示
                        $('.rt-bar').addClass('flt');
                        $('.rt-bar').css('position', 'fixed');
                    }
                    winSTbefore = winST;  //更新winSTbefore的值
                });


            })

        }
    };

    return Controller;
});