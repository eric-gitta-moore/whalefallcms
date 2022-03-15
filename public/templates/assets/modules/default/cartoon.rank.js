define(["jquery","./rank.js"],function ($,rank) {
    let type = "cartoon";

    let Controller = {
        index:function () {

            $(function () {

                return rank.index(type);

            })

        }
    };

    return Controller;
});