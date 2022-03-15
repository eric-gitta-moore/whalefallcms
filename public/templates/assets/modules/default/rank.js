define(["jquery",'./common/so.js','mescroll','./common/auto_task.js','swiper'],function ($,so,MeScroll,undefined,Swiper) {
    let Controller = {
        index:function () {
            so();

            //幻灯片
            var swiper = new Swiper('.focusbox .swiper-container', {
                spaceBetween: 30,
                centeredSlides: true,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                }
            });

            var mescroll = new MeScroll("mescroll", {
                down: {
                    auto: false,
                    callback: downCallback
                },
                up: {
                    callback: getListData,
                    isBounce: false,
                    clearEmptyId: "dataList",
                    toTop: {src: "/templates/assets/modules/default/static/mescroll-option/mescroll-totop.png", offset: 1000},
                    lazyLoad: {
                        use: true
                    }
                }
            });
            /*下拉刷新页面*/
            function downCallback() {
                setTimeout(function() {
                    window.location.reload();
                }, 300)
            }
            /*初始化菜单*/
            let order = orderby;
            // $("#readnum").trigger('click');
            $(".tabBar a").click(function() {
                let i = $(this).attr("id");
                if (order != i) {
                    order = i;
                    $(".tabBar a.selected").removeClass("selected");
                    $(this).addClass("selected");
                }
                mescroll.resetUpScroll();
                mescroll.hideTopBtn();
                $(".qhlist").addClass("animation");
                setTimeout(function() {
                    $(".qhlist").removeClass("animation")
                }, 300)
            });

            /*获取数据*/
            function getListData(page) {
                // var pageNum = page.num-1;
                // var pageSize = 15;
                $.ajax({
                    type: 'GET',
                    url: '/api/cartoon.book/getBooks/' +
                        'page/' + page.num +
                        '/size/' + page.size +
                        '/order/' + order,
                    dataType: 'json',
                    success: function(res) {
                        let data = res.data.data;
                        mescroll.endByPage(res.data.data.length, res.data.last_page);
                        setListData(data); //打印内容
                    },
                    error: function(e) {
                        mescroll.endErr();
                    }
                });
            }

            //设置显示
            function setListData(curPageData) {
                var listDom = document.getElementById("dataList");
                for (let i = 0; i < curPageData.length; i++) {
                    let pd = curPageData[i];
                    let str = '<div class="swiper-slide">';
                    str += '<a href="/index/' + module + '.book/detail/id/' + pd.id + '">' +
                        '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' +
                        '<b class="px">' + $('#' + order).attr('data-num') + '</b><p>' +
                        '<label>' + pd.author_name + '</label>' +
                        '<i>' + pd.status_text + '</i></p></a>' +
                        '<span class="booktitle">' + pd.name + '</span>' +
                        '<p class="commandDes">' + pd.last_chapter + '</p>';
                    str += '</div>';
                    let liDom = document.createElement("div");
                    liDom.innerHTML = str;
                    listDom.appendChild(liDom);
                    $('#' + order).attr('data-num',parseInt($('#' + order).attr('data-num')) + 1);
                }
            }


        }
    };

    return Controller;
});