define(["jquery", "./search.js"], function ($, searchjs) {
    let type = "cartoon";
    let Controller = {
        index: function () {
            $(function () {
                searchjs.index(type);


            })


        }
    };

    return Controller;


});