define(['jquery', 'bootstrap', 'frontend', 'form'], function ($, undefined, Frontend, Form) {
    let Controller = {
        exchange: function () {
            // $("#show").hide();
            $("input[name=money]").on("keyup", function () {
                $("#show").show()
                    .text("可兑换 " + score_name + " :" + parseFloat($(this).val()) * exchange_rate);

            });

            // $("input[type=submit]").on("click",function () {
            //
            //
            //
            // });

            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                setTimeout(function () {
                    location.href = ret.url;
                }, 1500);
            });

        }
    };

    return Controller;
});