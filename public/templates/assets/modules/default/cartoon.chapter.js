define(["./chapter.js"], function (chapter) {
    let Controller = {
        show: function () {
            let type = "cartoon";
            return chapter.show(type);

        }
    };

    return Controller;
});