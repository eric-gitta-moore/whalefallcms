define(['jquery', 'bootstrap', 'userend'], function ($, undefined, Userend) {
    var Controller = {
        index: function () {
            $(document).on("click", ".row-money label[data-type]", function () {
                $(".row-money label[data-type]").removeClass("active");
                $(this).addClass("active");
                $("#col-custom").toggleClass("hidden", $(this).data("type") === "fixed");
                $("input[name=money]").val($(this).data("value"));
                if ($(this).data("type") === 'custom') {
                    $("input[name=custommoney]").trigger("focus").trigger("keydown");
                }
            });
            $(document).on("click", ".row-paytype label", function () {
                $(".row-paytype label").removeClass("active");
                $(this).addClass("active");
                $("input[name=paytype]").val($(this).data("value"));
            });
            $(document).on("keyup", ".custommoney", function () {
                $("input[name=money]").val($(this).val());
            });
        }
    };
    return Controller;
});