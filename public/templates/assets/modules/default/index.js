define(["jquery",'swiper','mescroll','clipboard','./static/mescroll-option/mescroll-option.js','./common/so.js','./common/auto_task.js'],
    function ($,Swiper,MeScroll,undefined,undefined,so) {
    let Controller = {
        index:function () {
            so();

            //用途未知
            $("#waplink,#applink").addClass("selected");
            $("a#applink").click(function () {
                $("html,body").animate({scrollTop: 0}, 300)
            });

            //滑动
            new Swiper('.focusbox .swiper-container', {
                spaceBetween: 0,
                centeredSlides: true,
                loop: true,
                autoplay: {delay: 5000, disableOnInteraction: false,},
                pagination: {el: '.swiper-pagination', clickable: true}
            });
            new Swiper('.ranking_list .swiper-container', {
                spaceBetween: 0,
                centeredSlides: true,
                loop: true,
                autoplay: {delay: 3000, disableOnInteraction: false,}
            });
            let swiper = new Swiper('#swiperTabCon', {
                speed: 500, on: {
                    slideChangeTransitionStart: function () {
                        var index = swiper.activeIndex;
                        $('#swiperTabWrap ul li a').removeClass('active').eq(index).addClass('active');
                    }
                }
            });

            //排行 右侧 小按钮
            $('#swiperTabWrap ul li').on('click', function (e) {
                e.preventDefault();
                var i = $(this).index();
                $('#swiperTabWrap ul li a').removeClass('active').eq(i).addClass('active');
                swiper.slideTo(i, 300, false);
            });

            //上下拉初始化
            var mescroll = initMeScroll("mescroll", {
                down: {auto: false, callback: downCallback},
                up: {
                    auto: false,
                    isBoth: true,
                    callback: upCallback,
                    isBounce: false,
                    toTop: {src: "/templates/assets/modules/default/static/mescroll-option/mescroll-totop.png", offset: 1000},
                    lazyLoad: {use: true}
                }
            },MeScroll);

            //顶部下拉刷新
            function downCallback() {
                setTimeout(function () {
                    window.location.reload()
                }, 300)
            }

            //到底部上拉加载新数据
            function upCallback(page) {
                // let pageNum = page.num;
                // let pageSize = 10;
                $.ajax({
                    type: 'GET',
                    url: '/api/' + module + '.book/getBooks/page/' + page.num + '/size/' + page.size,
                    dataType: 'json',
                    success: function (res) {
                        let data = res.data;
                        mescroll.endByPage(data.data.length, data.last_page);
                        setListData(data.data)
                    },
                    error: function (e) {
                        mescroll.endErr()
                    }
                })
            }

            //置入数据
            function setListData(curPageData) {
                console.log(curPageData);
                let listDom = document.getElementById("dataList");
                for (let i = 0; i < curPageData.length; i++) {
                    let pd = curPageData[i];
                    let str = '<div class="swiper-slide ' + (pd.is_large==1?'large':'') + '">';
                    str += '<a href="/index/' + module + '.book/detail/id/' + pd.id + '">' +
                        '<span class="back" style="background-image: url(' + pd.back_image + ');"></span>' +
                        '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' + (pd['18plus']?'18+':'') +
                        '<p><label>' + pd.author_name + '</label><i>' + pd.status_text + '</i></p></a>';
                    str += '<span class="booktitle">' + pd.name + '</span><p class="commandDes">' + pd.last_chapter + '</p>';
                    str += '</div>';
                    let liDom = document.createElement("li");
                    liDom.innerHTML = str;
                    listDom.appendChild(liDom)
                }
            }
            // function setListData(curPageData) {
            //     console.log(curPageData);
            //     let listDom = document.getElementById("dataList");
            //     for (let i = 0; i < curPageData.length; i++) {
            //         let pd = curPageData[i];
            //         let str = '<div class="swiper-slide ' + (pd.is_large==1?'large':'') + '">';
            //         str += '<a href="/index/' + module + '.book/detail/id/' + pd.id + '">' +
            //             '<span class="back" style="background-image: url(' + pd.back_image + ');"></span>' +
            //             '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' + (pd['18plus']?'18+':'') +
            //             '<p><label>' + pd.last_chapter + '</label><i>' + pd.status_text + '</i></p></a>';
            //         str += '<span class="booktitle">' + pd.name + '</span><p class="commandDes">' + pd.summary + '</p>';
            //         str += '</div>';
            //         let liDom = document.createElement("li");
            //         liDom.innerHTML = str;
            //         listDom.appendChild(liDom)
            //     }
            // }


            //公共函数

            function GetQueryString(c) {
                c = new RegExp("(^|&)" + c + "=([^&]*)(&|$)");
                c = window.location.search.substr(1).match(c);
                return null != c ? unescape(c[2]) : null
            }

            function cookiesave(c, d, b, a, e) {
                c && (e || (e = "/"), b = new Date, b.setTime((new Date).getTime() + 864E5), b = "; expires=" + b.toGMTString(), a && (a = "domain=" + a + ";"), document.cookie = c + "=" + d + b + "; " + a + "path=" + e)
            }

            function cookieget(c) {
                c += "=";
                for (var d = document.cookie.split(";"), b = 0; b < d.length; b++) {
                    for (var a = d[b]; " " == a.charAt(0);) a = a.substring(1, a.length);
                    if (0 == a.indexOf(c)) return a.substring(c.length, a.length)
                }
                return ""
            }

            function PostClose() {
                $("#PostBox").hide();
                cookiesave("PostClose", "PostClose", "", "")
            }

            function clickclose() {
                "PostClose" == cookieget("PostClose") ? $("#PostBox").hide() : $("#PostBox").show()
            }

            window.onload = clickclose;


        }
    };

    return Controller;
});