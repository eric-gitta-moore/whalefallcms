define(["jquery", "lazyload", "layer","./common/reward.js","./common/collection.js","./common/lazyload.js"],
    function ($, undefined, layer,reward,collection,lazyLoad) {
    let Controller = {
        detail: function (type = "cartoon") {

            //加载打赏模块
            reward(type);

            //懒加载模块
            lazyLoad();

            //tab选项卡切换
            $("#btn_chapter").on("click", function () {
                $("#btn_detail").removeAttr("class");
                $("#book-info").hide();

                $(this).addClass("active");
                $("#book-chapters").show();
            });
            $("#btn_detail").on("click", function () {
                $(this).addClass("active");
                $("#book-info").show();

                $("#btn_chapter").removeAttr("class");
                $("#book-chapters").hide();
            });

            //监听加载全部章节按钮
            $("#btn_show_all_chapter").on("click", function () {
                $(".list > .item").show();
                $(this).hide();
                layer.msg("加载完成", {time: 500});
            });


            let noCollection = "<a id=\"add_collection\" class=\"btn\"><svg width=\"10\" height=\"10\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\"><path fill=\"#fff\" d=\"M20 8h-8V0H8v8H0v4h8v8h4v-8h8V8\"></path></svg>收藏</a>";
            let hasCollection = "<a id=\"cancel_collection\" class=\"btn gray\">已收藏</a>";
            let addCollectionSelector = "#add_collection";
            let cancelCollectionSelector = "#cancel_collection";
            let showCollectionListSelector = "#showcollect";
            let book_id = $("#more_reward").attr("data-book_id");
            collection(book_id,type,noCollection,hasCollection,addCollectionSelector,cancelCollectionSelector,showCollectionListSelector);

        }
    };

    return Controller;
});