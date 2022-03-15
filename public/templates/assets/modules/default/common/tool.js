define(['jquery'],function ($) {
    return {
        exploadCate:function (category,limit=2,split_str=' / ') {
            if (category == null)
            {
                return '';
            }
            if (category.length === 0)
            {
                return '';
            }
            let str = '';
            $.each(category,function (index,value) {
                if (index !== category.length - 1)
                {
                    str += value.name + split_str;
                }
                else
                {
                    str += value.name;
                }
            });
            return str;
        }
    }

});