define(['jquery', 'bootstrap', 'userend', 'form', 'template'], function ($, undefined, Userend, Form, Template) {

    var Controller = {
        index: function () {

            Form.api.bindevent($("#changepwd-form"));
        },
    };
    return Controller;
});