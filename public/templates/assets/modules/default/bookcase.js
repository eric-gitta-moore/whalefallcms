define(["jquery", "layer", 'mescroll', './common/auto_task.js', './common/tool.js', './common/mescroll-option.js'],
    function ($, layer, MeScroll, undefined, tool, initMeScroll) {
        let Controller = {
            index: function () {

                let mescroll = initMeScroll("mescroll", {
                    down: {
                        auto: false,
                        callback: downCallback
                    },
                    up: {
                        auto: true,
                        isBoth: true,
                        callback: upCallback,
                        isBounce: false,
                        lazyLoad: {
                            use: true
                        },
                        toTop: {
                            src: "/templates/assets/modules/default/static/mescroll-option/mescroll-totop.png",
                            offset: 1000
                        },
                    }
                });

                /*下拉刷新 */
                function downCallback() {
                    setTimeout(function () {
                        window.location.reload();
                    }, 300)
                }

                /*获取数据*/
                function upCallback(page) {
                    // var pageNum = page.num;
                    // var pageSize = page.size;
                    $.ajax({
                        type: 'GET',
                        url: '/api/user/collection/page/' + page.num + '/size/' + page.size,
                        dataType: 'json',
                        success: function (res) {
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

                //设置显示
                function setListData(curPageData) {
                    // curPageData = curPageData.book_detail;
                    // console.log(curPageData);
                    // console.log(curPageData.length);
                    let listDom = document.getElementById("dataList");
                    $.each(curPageData, function (index, value) {
                        let pd = value.book_detail;
                        // console.log(pd);
                        let str = '<li id="list-' + pd.id + '">';
                        str += '<a href="/index/' + (pd.novel_author_id === undefined ? 'cartoon' : 'novel') + '.book/detail/id/' + pd.id + '" class="fl">' +
                            (pd['18plus'] ? '18+' : '') +
                            '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' +
                            '<p>' + (pd.novel_author_id === undefined ? '漫画' : '小说') + '</p></a>' +
                            '<a href="/index/' + (pd.novel_author_id === undefined ? 'cartoon' : 'novel') + '.book/detail/id/' + pd.id + '" class="fr">' +
                            '<span class="name">' + pd.name + '</span><span class="txt">' +
                            '<div class="' + pd.del + ' hide">上次浏览第：' + pd.Progress + ' 章</div></span>' +
                            '<span class="txt">已更新至第：' + pd.last_chapter + ' 话</span>' +
                            '<span class="txt">作者：' + pd.author_name + '</span></a>';
                        str += '<a onclick="delete_item(this)" class="delete-collect-btn del" data-book_id="' + pd.id + '" data-type="' + (pd.novel_author_id === undefined ? 'cartoon' : 'novel') + '" href="javascript:;"></a>';
                        str += '</li>';
                        let liDom = document.createElement("div");
                        liDom.innerHTML = str;
                        listDom.appendChild(liDom);
                    });

                    // for (let i = 0; i < curPageData.length; i++) {
                    //     let pd = curPageData[i].book_detail;
                    //     console.log(pd);
                    //     let str = '<li id="list-' + pd.id + '">';
                    //     str += '<a href="/index/' + module + '.book/detail/id/' + pd.id + '" class="fl">' +
                    //         (pd['18plus']?'18+':'')  +
                    //         '<img src="/templates/assets/modules/default/static/images/cover.jpg" imgurl="' + pd.cover_image + '">' +
                    //         '<p class="hide">' + '</p></a>' +
                    //         '<a href="/index/' + module + '.book/detail/id/' + pd.id + '" class="fr">' +
                    //         '<span class="name">' + pd.name + '</span><span class="txt">' +
                    //         '<div class="' + pd.del + '">上次浏览第：' + pd.Progress + ' 章</div></span>' +
                    //         '<span class="txt">已更新至第：' + pd.last_chapter + ' 话</span>' +
                    //         '<span class="txt">作者：' + pd.author_name + '</span></a>';
                    //     str += '<a class="' + pd.del + '" href="javascript:;" onclick="user.vote(\'' + pd.Id + '\',\'1\',\'4\',\'list-' + pd.Id + '\',this);"></a>';
                    //     str += '</li>';
                    //     let liDom = document.createElement("div");
                    //     liDom.innerHTML = str;
                    //     listDom.appendChild(liDom);
                    // }
                }

                $("#delall").on('click',function () {
                    layer.confirm('是否继续', function () {
                        $.ajax({
                            type: "get",
                            url: "/api/user/cancelCollectionAll",
                            data:{
                                data:'all'
                            },
                            complete: function (xhr) {
                                let res = xhr.responseJSON;
                                layer.msg(res.msg);
                                if (res.code === 1)
                                {
                                    $('#dataList').children().remove();
                                }
                            },
                        })
                    });
                });

                // console.log(2);
                window.delete_item = function (that)
                {
                    let this_book_id = $(that).attr('data-book_id');
                    let this_book_selector = '#list-' + this_book_id;
                    layer.confirm('是否继续', function () {
                        $.ajax({
                            type: "get",
                            url: "/api/user/cancelCollection/book_id/" + this_book_id + '/type/' + $(that).attr('data-type'),
                            complete: function (xhr) {
                                let res = xhr.responseJSON;
                                layer.msg(res.msg);
                                if (res.code === 1)
                                {
                                    $(this_book_selector).parent('div').remove();
                                }
                            },
                        })
                    });
                };

                // $("a.del.delete-collect-btn").on('click',function () {
                //     // console.log(1);
                //     layer.confirm('是否继续', function () {
                //         $.ajax({
                //             type: "get",
                //             url: "/api/user/cancelCollection/book_id/" + $(this).attr('data-book_id') + '/type/' + $(this).attr('data-type'),
                //             complete: function (xhr) {
                //                 let res = xhr.responseJSON;
                //                 layer.msg(res.msg);
                //                 if (res.code === 1)
                //                 {
                //                     $(this).remove();
                //                 }
                //             },
                //         })
                //     });
                // });

            },
            consumption:function () {
                let mescroll = initMeScroll("mescroll", {
                    down: {
                        auto: false,
                        callback: downCallback
                    },
                    up: {
                        auto: true,
                        isBoth: true,
                        callback: upCallback,
                        isBounce: false,
                        lazyLoad:{
                            use:true
                        },
                        toTop: {
                            src: "/templates/assets/modules/default/static/mescroll-option/mescroll-totop.png",
                            offset: 1000
                        },
                    }
                });
                /*下拉刷新 */
                function downCallback() {
                    setTimeout(function() {
                        window.location.reload();
                    }, 300)
                }
                /*获取数据*/
                function upCallback(page) {
                    $.ajax({
                        type: 'GET',
                        url: '/api/user/consumption/page/' + page.num + '/size/' + page.size,
                        dataType: 'json',
                        success: function(res) {
                            mescroll.endByPage(res.data.data.length, res.data.last_page);
                            $('.tabBot').children('span').children('b').text(res.data.total);
                            setListData(res.data.data); //打印内容
                        },
                        error: function(e) {
                            mescroll.endErr();
                        }
                    });
                }
                //设置显示
                function setListData(curPageData) {
                    let listDom = document.getElementById("dataList");
                    for (let i = 0; i < curPageData.length; i++) {
                        let pd = curPageData[i];
                        let str = '<a href="/index/' + pd.type + '.chapter/show/id/' + pd.chapter_id + '">' +
                            pd.chapter_name + '《' + pd.book_name + '》' + '<span>' + pd.create_time_text + '</span></a>';
                        let liDom = document.createElement("li");
                        liDom.innerHTML = str;
                        listDom.appendChild(liDom);
                    }
                }

            },
            history:function () {

            }
        };

        return Controller;

    });