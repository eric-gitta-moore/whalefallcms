define(['jquery', 'bootstrap', 'frontend', 'template'], function ($, undefined, Frontend, Template) {
    var Controller = {
        index: function () {
            var isMobile = !!("ontouchstart" in window);
            $(document).on("click", ".btn-signin,.today", function () {
                Fast.api.ajax("signin/dosign", function () {
                    Layer.msg("签到成功!", {
                        time: 2500
                    }, function () {
                        location.reload();
                    });
                });
                return false;
            });
            if (Config.isfillup) {
                $(document).on("click", ".expired[data-date]:not(.today):not(.signed)", function () {
                    var that = this;
                    Layer.confirm("确认进行补签日期：" + $(this).data("date") + "？<br>补签将消耗" + Config.fillupscore + " 积分", function () {
                        Fast.api.ajax("signin/fillup?date=" + $(that).data("date"), function (data, ret) {
                            Layer.msg("补签成功!", {
                                time: 1500
                            }, function () {
                                location.reload();
                            });
                            return false;
                        }, function (data, ret) {
                            Layer.alert(ret.msg);
                            return false;
                        });
                    });
                    return false;
                });
            }
            $(document).on("click", ".btn-rule", function () {
                Layer.open({
                    title: '签到积分规则',
                    content: Template("signintpl", {}),
                    btn: []
                });
                return false;
            });
            $(document).on("click", ".btn-rank", function () {
                Fast.api.ajax("signin/rank", function (data) {
                    Layer.open({
                        title: '签到排行榜',
                        type: 1,
                        zIndex: 88,
                        area: isMobile ? 'auto' : ["400px"],
                        content: Template("ranktpl", data),
                        btn: []
                    });
                    return false;
                });
                return false;
            });
        }
    };
    return Controller;
});