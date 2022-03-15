define(["jquery","layer"],function ($,layer) {
    let collection = function (bookId,type = "cartoon",notCollectionHtml,hasCollectionHtml,addCollectionSelector,cancelCollectionSelector,showCollectionListSelector) {
        //监听收藏按钮
        bind();

        function bind() {
            $(addCollectionSelector).on("click", function () {
                $(showCollectionListSelector).html(hasCollectionHtml);
                $.ajax({
                    url:"/api/user/addCollection/book_id/" + bookId + "/type/" + type,
                    complete:function (xhr,statusCode) {
                        let res = xhr.responseJSON;
                        console.log(xhr);
                        console.log(res);
                        if (res.code === 1)//code=1为成功
                        {
                            layer.msg(res.msg);
                        } else {
                            $(showCollectionListSelector).html(notCollectionHtml);
                            layer.msg(res.msg);
                        }
                        bind();
                    },
                });
            });

            $(cancelCollectionSelector).on("click", function () {
                $(showCollectionListSelector).html(notCollectionHtml);
                $.ajax({
                    url:"/api/user/cancelCollection/book_id/" + bookId + "/type/" + type,
                    complete:function (xhr,statusCode) {
                        let res = xhr.responseJSON;
                        console.log(xhr);
                        console.log(res);
                        if (res.code === 1)//code=1为成功
                        {
                            layer.msg(res.msg);
                        } else {
                            $(showCollectionListSelector).html(hasCollectionHtml);
                            layer.msg(res.msg);
                        }
                        bind();
                    },
                });
            });
            //
            //
            // $(cancelCollectionSelector).on("click", function () {
            //     $(showCollectionListSelector).html(notCollectionHtml);
            //     $.get("/api/user/cancelCollection/book_id/" + bookId + "/type/" + type, {}, function (res) {
            //         console.log(res);
            //         if (res.code === 1)//code=1为成功
            //         {
            //             layer.msg(res.msg);
            //         } else {
            //             $(showCollectionListSelector).html(hasCollectionHtml);
            //             layer.msg(res.msg);
            //         }
            //         bind();
            //     })
            // });
        }
    };

    return collection;
});