define(["jquery","./cate.js"],function ($,cate) {
    let Controller = {
        index:function () {

            $(function () {

                return  cate.index();

            })

        }
    };

    return Controller;


});