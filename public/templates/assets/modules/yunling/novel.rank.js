define(["jquery","./rank.js"],function ($,rank) {
    let type = "novel";

    let Controller = {
        index:function () {

            $(function () {

                return rank.index(type);

            })

        }
    };

    return Controller;
});