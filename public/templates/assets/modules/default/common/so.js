define(['jquery'],function ($) {
    return function () {

        //顶部搜索
        let floatSo = $(".floatSo").offset().top - 0;
        $(window).scroll(function() {
            var sTop = document.documentElement.scrollTop == 0 ? document.body.scrollTop : document.documentElement.scrollTop;
            if (sTop >= floatSo) {
                $("#floatSo,main#left,main#right").addClass("float");
            } else {
                $("#floatSo").removeClass("float");
            }
        });
        $(".searchInput").click(function() {
            $("#searchlist").addClass("animation");
            $("#floatSo,main#left,main#right").addClass("click");
            $("html,body").css({
                height: "100%"
            });
            $("#cancleBtn").show()
        });
        $("#cancleBtn").click(function() {
            $("#searchlist").removeClass("animation");
            $("#floatSo").removeClass("click");
            $("html,body").css({
                height: "auto"
            });
            $("#cancleBtn").hide()
        });
    }
});