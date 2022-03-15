define(['jquery', 'bootstrap', 'frontend', 'form'], function ($, undefined, Frontend, Form) {
    var Controller = {
        withdraw: function () {
            $("form[role=form]").data("validator-options", {
                rules: {
                    account: function (element) {
                        return this.test(element, "mobile") === true ||
                            this.test(element, "email") === true ||
                            '请填写手机号或者邮箱';
                    }
                }
            });
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                setTimeout(function () {
                    location.href = ret.url;
                }, 1500);
            });
        }
    };
    return Controller;
});