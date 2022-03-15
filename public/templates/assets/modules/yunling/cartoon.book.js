define(["jquery","./book.js"],function ($,bookjs) {
    let Controller = {
        detail: function () {
            let type = "cartoon";
            bookjs.detail(type);
        }
    };

    return Controller;
});