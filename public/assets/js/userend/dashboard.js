define(['jquery', 'bootstrap', 'userend', 'table', 'form', 'echarts', 'echarts-theme'], function ($, undefined, Userend, Table, Form, Echarts, undefined) {

    var Controller = {
        index: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };

    return Controller;
});