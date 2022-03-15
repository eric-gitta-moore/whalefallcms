define(["jquery","./book.js"],function ($,bookjs) {
    let Controller = {
        detail: function () {
            let type = "novel";
            bookjs.detail(type);
        }
    };

    return Controller;
});