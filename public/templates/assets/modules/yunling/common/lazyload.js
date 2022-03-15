define(["jquery","lazyload"],function ($) {
    return function (imgSelector="img.lazy") {
        $(function () {
            $(imgSelector).lazyload({
                threshold: 100,
                // effect: "fadeIn",
                placeholder:loadingImage,
                // event:"sporty",
            });

        })
    }

});