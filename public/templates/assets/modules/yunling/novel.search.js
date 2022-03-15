define(["jquery", "./search.js"], function ($, searchjs) {
    let type = "novel";
    let Controller = {
        index: function () {
            $(function () {
                searchjs.index(type);


            })


        }
    };

    return Controller;


});