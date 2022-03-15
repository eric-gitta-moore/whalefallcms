define(['jquery'],function ($) {

    if (auto_task_urls.length > 0)
    {
        let interval = setInterval(function () {
            if (document.readyState == 'complete')
            {
                $.each(auto_task_urls,function (index,value) {
                    $.get(value);
                });
                clearInterval(interval);
            }

        },1000);
    }

});