define(["jquery"], function ($) {
    return {
        index: function (type = "cartoon") {
            //回车自动提交
            $('#search_keyword').on("keyup",function (event) {
                keyup();
            });

            keyup();

            function keyup()
            {
                let key_v = $('#search_keyword').val();
                if (key_v == '') {
                    $('#btn_cancel').show();
                    $('#btn_search').hide();
                }
                else
                {
                    $('#btn_cancel').hide();
                    $('#btn_search').show();
                }
                if (event.keyCode === 13) {
                    do_search();
                }
            }

            $('#search_keyword').focus();

            function do_search() {
                let key_v = $('#search_keyword').val();
                window.location.href = "/index/" + type + ".search/index/search_word/" + encodeURI(key_v);

            }

            $("#btn_search").on("click",function () {
                do_search();

            })

        }
    }

});