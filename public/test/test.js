//内容参数
var b = $("#id").val(),
    a = $("#cid").val(),
    d = $("#zid").val(),
    g = $("#numid").val();
$.getScript("/json/link/?id=" + b + "&cid=" + a + "&zid=" + d + "&nid=" + g + "&type=2");
$(function(){
    imageloading();
    sidelink();
    feedback();
});
//图片加载
function imageloading() {
    $.ajax({
        url: "/json/Info",
        data: {
            cid: a,
            id: b,
            type: "1"
        },
        type: "get",
        dataType: "json",
        async: !0,
        success: function(l) {
            var k = "";
            $.each(l.data, function(c, h) {
                c = h.list;
                e = h.code;
                f = h.tips;
                var l = $("#litemppic").html();
                $.each(c, function(c, h) {
                    k += l.replace(/{img}/g, h.img)
                })
            });
            1 == f && myTips("成功购买章节");
            0 == e && ($(".showimg").addClass("height"), $("#needPay").addClass("is-visible"), $(".mask").show());
            $("#showimgcontent").after(k);
            original();
            /*
            setTimeout(function(){
             nextload(); //延迟20秒再预加载下章
            }, 20000);
            */
        }
    });
};
//侧边列表
function sidelink(){
    $.ajax({
        url: "/json/link",
        data: {
            cid: a,
            id: b,
            type: "5"
        },
        type: "get",
        dataType: "json",
        async: !0,
        success: function(l) {
            var k = "";
            $.each(l.data, function(c, h) {
                c = h.list;
                var l = $("#litemptxt").html();
                $.each(c, function(c, h) {
                    k += l.replace(/{name}/g, h.name).replace(/{nlink}/g, h.url).replace(/{id}/g, h.id)
                })
            });
            $("#listtext").after(k)
        }
    })
};
/*
//预载函数
$(function(l) {
	function k(c, h) {
		this.imgs = "string" === typeof c ? [c] : c;
		this.opts = l.extend({}, k.DEFAULTS, h);
		this._orderedLoad();
		k.DEFAULTS = {
			order: "ordered",
			each: null,
			all: null
		}
	}
	k.prototype._orderedLoad = function() {
		function c() {
			var h = new Image;
			h.src = k[m];
			l(h).on("load error", function() {
				m > p - 1 ? n.all && n.all() : (n.each && n.each(m), c());
				m++
			})
		}
		var k = this.imgs,
			n = this.opts,
			m = 0,
			p = k.length;
		c()
	};
	l.extend({
		preload: function(c, h) {
			new k(c, h)
		}
	})
});
//预载下章
function nextload() {
	$.get("/json/Info/?cid=" + a + "&id=" + nextnum + "&type=2&load=next", function(l, k) {
		$.preload(imgs, {
			order: "unordered",
			each: function(c) {
				console.log("预加载下章第" + (c + 1) + "张图片")
			},
			all: function() {
				console.log("下章加载完成")
			}
		})
	})
}
*/
//错误反馈
function feedback() {
    $("#rbtn").unbind("click").click(function(l, k) {
        $("#outwin").addClass("is-visible");
        k = $(this).attr("data-url");
        $(".submit-button").click(function() {
            var c = $("input[name='title']:checked").val(),
                h = $("#errortext").val();
            $.ajax({
                type: "POST",
                url: "/e/extend/feedback.php?",
                data: "enews=AddFeedback&bid=1&ajax=1&url=" + k + "&title=" + c + "&saytext=" + h,
                success: function(c, h) {
                    "200" == c ? (myTips("提交成功，感谢您的支持！"), $(".form-text").fadeOut(), $("#outwin").removeClass("is-visible"), $("#errortext").val("")) : myTips("系统错误，提交失败！")
                }
            });
            return !1
        });
        $(".form-line input").click(function() {
            $(".form-text").fadeOut()
        });
        $(".form-line input.other").click(function() {
            $(".form-text").fadeIn()
        });
        $(".outwin-title a,.cancel-button").click(function() {
            $(".form-text").fadeOut();
            $("#outwin").removeClass("is-visible")
        })
    })
};
//左右键翻页
$(document).on("keydown", function(event) {
    switch (event.keyCode) {
        case 37:
            window.location.href = $('#pre').attr('href');
            break;
        case 39:
            window.location.href = $('#next').attr('href');
            break;
    }
});	