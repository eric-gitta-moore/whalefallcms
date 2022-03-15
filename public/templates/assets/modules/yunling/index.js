define(['jquery',"./tools.js","./common/lazyload.js","slick",'./common/auto_task.js'],
    function ($,tools,lazyLoad,undefined,undefined) {
    var Controller = {
        common:function()
        {
            //懒加载模块
            lazyLoad();

            $('.portal-slick').slick({
                arrows: false,
                dots: true,
                autoplay: true,
                autoplaySpeed: 3000,
                adaptiveHeight: true
            });
        },

        index:function () {
            $(function(){
                Controller.common();

            });
        },

        cartoon:function() {
            Controller.index();
        },

        novel:function () {

            $(function () {

                Controller.common();




            })

        },

        listen:function () {

            $(function () {
                Controller.common();


            })

        }
    };
    return Controller;
});