define(["jquery", "layer", "mescroll"],
    function ($, layer, MeScroll, undefined, undefined) {
        let Controller = {
            detail: function (type = "cartoon") {
                $("#cate").addClass("selected");

                Date.prototype.format = function (fmt) {
                    var o = {
                        "M+": this.getMonth() + 1,                 //月份
                        "d+": this.getDate(),                    //日
                        "h+": this.getHours(),                   //小时
                        "m+": this.getMinutes(),                 //分
                        "s+": this.getSeconds(),                 //秒
                        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                        "S": this.getMilliseconds()             //毫秒
                    };
                    if (/(y+)/.test(fmt)) {
                        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
                    }
                    for (var k in o) {
                        if (new RegExp("(" + k + ")").test(fmt)) {
                            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
                        }
                    }
                    return fmt;
                };

                //章节列表
                let mescroll = new MeScroll("mescroll", {
                    down: {
                        auto: false,
                        callback: downCallback
                    },
                    up: {
                        callback: getListData,
                        isBounce: false,
                        clearEmptyId: "dataList",
                        toTop: {
                            src: "/templates/assets/modules/default/static/mescroll-option/mescroll-totop.png", offset: 1000
                        },
                        lazyLoad: {
                            use: true
                        }
                    }
                });

                /*下拉刷新页面*/
                function downCallback() {
                    setTimeout(function () {
                        window.location.reload();
                    }, 300)
                }

                /*切换排序*/
                var order = 'asc';
                $(".navorder a").click(function () {
                    var i = $(this).attr("id");
                    if (order != i) {
                        order = i;
                        $(".navorder a.active").removeClass("active");
                        $(this).addClass("active");
                        mescroll.resetUpScroll();
                        mescroll.hideTopBtn();
                        $(".qhlist").addClass("animation");
                        setTimeout(function () {
                            $(".qhlist").removeClass("animation")
                        }, 300)
                    }
                });

                /*获取数据*/
                function getListData(page) {
                    // var pageNum = page.num - 1;
                    // var pageSize = page.size;
                    // var zpid = $("#snovelId").val();
                    $.ajax({
                        type: 'GET',
                        url: '/api/' + module + '.book/getChapterList/id/' + book_info.id + '/page/' + page.num + '/size/' + page.size + '/orderby/' + order,
                        dataType: 'json',
                        success: function (res) {
                            $(".data-list").html();
                            // var curPageData = b.length; //内容列表
                            // var totalPage = b.totalPage; //总页码
                            mescroll.endByPage(res.data.data.length, res.data.last_page);
                            setListData(res.data.data); //打印内容
                        },
                        error: function (e) {
                            mescroll.endErr();
                        }
                    });
                }

                /*设置列表数据*/
                function setListData(curPageData) {
                    let listDom = document.getElementById("dataList");
                    for (let i = 0; i < curPageData.length; i++) {
                        let pd = curPageData[i];
                        // let gold = $("#gold").val();
                        console.log(pd);

                        let t = "免费", s = "free";
                        // 1 == pd.price ? s = "free" :
                        //     2 == pd.price ? s = "toll" :
                        //         3 == pd.price ? s = "vip" :
                        //             4 == pd.price ? s = "bought" :
                        //                 5 == pd.price ? s = "read" :
                        //                     6 == pd.price ? s = "his" :
                        //                         7 == pd.price && (s = "free");
                        // 1 == pd.price ? t = "免费" :
                        //     2 == pd.price ? t = gold + "阅币" :
                        //         3 == pd.price ? t = "VIP免费" :
                        //             4 == pd.price ? t = "已购买" :
                        //                 5 == pd.price ? t = "已看过" :
                        //                     6 == pd.price ? t = "上次看到这里" :
                        //                         7 == pd.price && (t = "限免中");

                        if (pd.money != 0) {
                            s = 'vip';
                            t = pd.money.toString() + Config.site.score_name + ' | VIP免费';
                        }

                        if (/(iPhone|iPad|iPod|iOS|Android)/i.test(navigator.userAgent)) {
                            $(".data-list a,#startUrl").attr("target", "_self")
                        }

                        let str = '<a href="/index/' + module + '.chapter/show/id/' + pd.id + '" target="_blank">' +
                            '<span class="imgs fl">' +
                            '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.image + '">' +
                            '</span>' +
                            '<span class="w50">' + pd.name + '<p>' +
                            new Date(parseInt(pd.log_time != null ?
                                pd.log_time.toString() + '000' :
                                pd.updatetime.toString() + '000')).format('yyyy-MM-dd') +
                            '</p></span>' +
                            '<b class="' + s + '">' + t + '</b>' +
                            '</a>';
                        let liDom = document.createElement("li");
                        liDom.innerHTML = str;
                        listDom.appendChild(liDom);
                    }
                }

                if ($("#collection_btn").hasClass('collected')) {
                    $("#collection_btn").on('click', cancel_collection);
                } else {
                    $("#collection_btn").on('click', add_collection);
                }

                function add_collection() {
                    $.ajax({
                        url: "/api/user/addCollection/book_id/" + book_info.id + "/type/" + type,
                        complete: function (xhr, statusCode) {
                            let res = xhr.responseJSON;
                            console.log(xhr);
                            console.log(res);
                            if (res.code === 1)//code=1为成功
                            {
                                layer.msg(res.msg);
                                $("#collect-name").text('已收藏');
                                $("#collect-num").text(parseInt($("#collect-num").text()) + 1);
                            } else {
                                // $(showCollectionListSelector).html(notCollectionHtml);
                                layer.msg(res.msg);
                            }
                        },
                    });
                    $("#collection_btn").unbind('click',add_collection).bind('click',cancel_collection);
                }

                function cancel_collection() {
                    $.ajax({
                        url: "/api/user/cancelCollection/book_id/" + book_info.id + "/type/" + type,
                        complete: function (xhr, statusCode) {
                            let res = xhr.responseJSON;
                            console.log(xhr);
                            console.log(res);
                            if (res.code === 1)//code=1为成功
                            {
                                layer.msg(res.msg);
                                $("#collect-name").text('收藏');
                                $("#collect-num").text(parseInt($("#collect-num").text()) - 1);
                            } else {
                                layer.msg(res.msg);
                            }
                        },
                    });
                    $("#collection_btn").unbind('click',cancel_collection).bind('click',add_collection);
                }


                /*点赞收藏**/
                // user = {
                //     vote: function(a, b, c, e, d) {
                //         $.ajax({
                //             type: "get",
                //             url: "/json/fav/add/?btn=yes&type=" + c + "&id=" + a + "&classid=" + b,
                //             success: function(a) {
                //                 "yes" == a ? (myTips("已加入书架"), $("#add").hide(), $("#del").show()) : "on" == a ? (myTips("删除成功"), $("#add").show(), $("#del").hide()) : myTips("请先登陆会员")
                //             }
                //         })
                //     }
                // };

                // SQ = {
                //     thispostion: function(a) {
                //         var b = $(a).offset().left;
                //         a = $(a).offset().top + $(a).height();
                //         return {
                //             x: b,
                //             y: a
                //         }
                //     },
                //     windowpostion: function(a) {
                //         a = $(window).width() / 2 + $(window).scrollLeft();
                //         var b = $(window).height() / 2 + $(window).scrollTop();
                //         return {
                //             x: a,
                //             y: b
                //         }
                //     },
                //     mouseposition: function(a) {
                //         var b = 0,
                //             c = 0;
                //         a = a || window.event;
                //         if (a.pageX || a.pageY) b = a.pageX, c = a.pageY;
                //         else if (a.clientX || a.clientY) b = a.clientX + document.body.scrollLeft + document.documentElement.scrollLeft, c = a.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                //         return {
                //             x: b,
                //             y: c
                //         }
                //     },
                //     Ajax: function(a) {
                //         a = $.extend({
                //             type: "post",
                //             data: "",
                //             dataType: "jsonp",
                //             before: function() {}
                //         }, a);
                //         burl = (-1 == a.request.indexOf("?") ? "?" : "&") + "_rnd=" + (new Date).getTime();
                //         $.ajax({
                //             type: a.type,
                //             url: a.request + burl,
                //             data: a.data,
                //             dataType: a.dataType,
                //             beforeSend: a.before,
                //             success: a.respon
                //         })
                //     },
                //     Ajax_async: function(a) {
                //         a = $.extend({
                //             type: "post",
                //             data: "",
                //             dataType: "jsonp",
                //             before: function() {}
                //         }, a);
                //         burl = (-1 == a.request.indexOf("?") ? "?" : "&") + "_rnd=" + (new Date).getTime();
                //         $.ajax({
                //             type: a.type,
                //             url: a.request + burl,
                //             async: !1,
                //             data: a.data,
                //             dataType: a.dataType,
                //             beforeSend: a.before,
                //             success: a.respon
                //         })
                //     },
                //     ajaxLoginCheck: function(a) {
                //         return "0" == a.is_login ? (SQ.Adiv(a), !1) : !0
                //     },
                //     boolIe: function() {
                //         return $.browser.msie && "6.0" == $.browser.version ? !0 : !1
                //     }
                // };
                // Digg = {
                //     vote: function(a, b, c, e, d) {
                //         $(".act-msg").remove();
                //         SQ.Ajax({
                //             request: "/e/extend/digg.php?id=" + a + "&classid=" + b + "&type=" + c,
                //             data: "",
                //             respon: function(b) {
                //                 if (!1 !== SQ.ajaxLoginCheck(b)) {
                //                     if (403 == b.status) return myTips(b.msg, "error"), !1;
                //                     var c = $(d).offset().left + 50,
                //                         f = $(d).offset().top - 20,
                //                         g = f - 30;
                //                     $("body").append("<div id='act-msg-" + a + "' class='act-msg " + b.code + "'><div class='layer-inner'>" + b.msg + "</div><em></em></div>");
                //                     $("#act-msg-" + a).css({
                //                         position: "absolute",
                //                         left: c,
                //                         top: f,
                //                         "z-index": "99999999"
                //                     }).animate({
                //                         top: g
                //                     }, 300);
                //                     setTimeout(function() {
                //                         $("#act-msg-" + a).fadeOut("200")
                //                     }, 1E3);
                //                     $("#" + e).html(b.count)
                //                 }
                //             }
                //         })
                //     }
                // };
                /*COOKIES浏览记录*
                $(document).ready(function() {
                    if (localStorage.getItem(CartoonId)) {
                        var a = localStorage.getItem(CartoonId).split(",");
                        $("#startUrl").attr("href", a[0]);
                        $("#startUrl").html("继续阅读 <em>(第" + a[1] + "话)</em>");
                        $("#history").html('<li style="background:#fafafa;"><a href="' + a[0] + '"><span class="imgs fl"><img src="' + a[3] + '"></span><span class="w50 red"><p class="timeIcon">上次浏览到</p>&nbsp;&nbsp;&nbsp;' + a[2] + "</a><b class='his'>继续阅读</b></a></li>")
                    } else $("#startUrl").html("开始阅读 <em>(第1话)</em>"), $("#history").hide()
                });
                */
            }
        };

        return Controller;
    });