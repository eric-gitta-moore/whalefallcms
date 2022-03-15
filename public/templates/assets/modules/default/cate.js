define(["jquery",'swiper','mescroll','clipboard','./static/mescroll-option/mescroll-option.js','./common/so.js','./common/auto_task.js'],
    function ($,Swiper,MeScroll,undefined,undefined,so) {
    let Controller = {
        index:function () {
            so();

            $("#cate").addClass("selected");

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

            /*切换*/
            $(".catebox a").click(function() {
                $(".qhlist").addClass("animation");
                setTimeout(function() {
                    $(".qhlist").removeClass("animation")
                }, 300)
            });

            /*题材*/
            $(".stc a").click(function() {
                let i = $(this).attr("data-value");
                if (cate != i) {
                    cate = i;
                    $(".stc a.red").removeClass("red");
                    $(this).addClass("red");
                }
                mescroll.resetUpScroll();
                mescroll.hideTopBtn();
            });

            /*读者*/
            $(".sdz a").click(function() {
                let i = $(this).attr("data-value");
                if (reader != i) {
                    reader = i;
                    $(".sdz a.red").removeClass("red");
                    $(this).addClass("red");
                }
                mescroll.resetUpScroll();
                mescroll.hideTopBtn();
            });

            /*状态*/
            $(".szt a").click(function() {
                let i = $(this).attr("data-value");
                if (status != i) {
                    status = i;
                    $(".szt a.red").removeClass("red");
                    $(this).addClass("red");
                }
                mescroll.resetUpScroll();
                mescroll.hideTopBtn();
            });

            /*排序*/
            $(".spx a").click(function() {
                let i = $(this).attr("data-value");
                if (orderby != i) {
                    orderby = i;
                    $(".spx a.red").removeClass("red");
                    $(this).addClass("red");
                }
                mescroll.resetUpScroll();
                mescroll.hideTopBtn();
            });

            /*获取数据*/
            function getListData(page) {
                // var pageNum = page.num - 1;
                // var pageSize = 16;
                $.ajax({
                    type: 'GET',
                    url: '/api/cartoon.book/getBooks/' +
                        'page/' + page.num +
                        '/size/' + page.size +
                        '/order/' + orderby +
                        '/cate/' + cate +
                        '/render/' + reader +
                        '/status/' + status,
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

            //置入数据
            function setListData(curPageData) {
                var listDom = document.getElementById("dataList");
                for (var i = 0; i < curPageData.length; i++) {
                    var pd = curPageData[i];
                    var str = '<div class="swiper-slide ' + (pd.is_large?'large':'') + '">';
                    str += '<a href="/index/' + module + '.book/detail/id/' + pd.id + '">' +
                        '<span class="back" style="background-image: url(' + pd.back_image + ');"></span>' +
                        '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' +
                        (pd['18plus']?'18+':'') +
                        '<p><label>' + pd.author_name + '</label>' +
                        '<i>' + pd.status_text + '</i></p></a>';
                    str += '<span class="booktitle">' + pd.name + '</span><p class="commandDes">' + pd.last_chapter + '</p>';
                    str += '</div>';
                    var liDom = document.createElement("div");
                    liDom.innerHTML = str;
                    listDom.appendChild(liDom);
                }
            }

        }
    };

    return Controller;
});