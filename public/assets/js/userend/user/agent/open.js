define(['jquery', 'bootstrap', 'userend'], function ($, undefined, Userend) {
    var Controller = {
        buy: function () {
            $(document).on("click", "#chargebox .service-list", function () {
                $(".service-list").removeClass("actives");
                $(this).addClass("actives");
                var htmls = $(this).find('.quanxian').html();
                $('#viewtq').html(htmls);

                var money = $(this).attr('data-money');
                var time = $(this).attr('data-time');
                $('#countNum').text(money);
                $('#validity').text(time);
                $('input[name=buygroupid]').val($(this).attr('data-id'));
            });
            $(document).on("click", "#charge-source-list .item", function () {
                $(".item").removeClass("active");
                $(this).addClass("active");
                $("input[name=paytype]").val($(this).data("value"));
            });
        }
    };
    return Controller;
});