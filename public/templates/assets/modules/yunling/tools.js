define(["jquery"],function ($) {
    var tools = {
        tips:function (msg,timeout=2000) {
            var oMask = document.createElement("div");
            oMask.id = "msg_tips";
            oMask.style.position="fixed";
            oMask.style.left="0";
            oMask.style.top="50%";
            oMask.style.zIndex="100";
            oMask.style.textAlign="center";
            oMask.style.width="100%";
            oMask.innerHTML =  "<span style='background: rgba(0, 0, 0, 0.65);color: #fff;padding: 10px 15px;border-radius: 3px; font-size: 14px;'>" + msg + "</span>";
            document.body.appendChild(oMask);
            window.setTimeout(function(){$("#msg_tips").remove();},timeout);
        }
    };
    return tools;

});