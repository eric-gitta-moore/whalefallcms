define(["jquery"],function ($) {
   $(function () {

      if (cmod.indexOf("chapter") === -1)
      {
         return false;
      }

      //记录历史
      let his_str = localStorage.getItem("history"),
          his;
      if (his_str)
      {
         his = JSON.parse(his_str);
      }
      else
      {
         his = JSON.parse("{\"data\":[]}");
      }

      // console.log(his.data);

      let flag = true;

      $.each(his.data,function (i,datum) {
         // console.log(datum.id);
         if (book_info.id === datum.id)
         {
            flag = false;
            return false;
         }

      });

      //flag为真，则添加
      if (flag)
      {
         book_info.module = module;
         his.data.push(book_info);
         localStorage.setItem("history",JSON.stringify(his));
      }


   })

});