define(['jquery', 'bootstrap', 'frontend', 'form', 'template'], function ($, undefined, Frontend, Form, Template) {
    var Controller = {
        index: function () {
            require(['../addons/invite/js/clipboard.min'], function (Clipboard) {
                var clipboard = new Clipboard('.btn-invite');
                clipboard.on('success', function (e) {
                    Toastr.success("邀请链接已复制到剪贴板!");
                    e.clearSelection();
                });
            });
        },
    };
    return Controller;
});