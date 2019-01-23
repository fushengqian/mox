var view = 0;
$("#view_by_1").bind('click', function(){
     if (view == 1)
     {
         $("#view_1").css("display", "none");
         $("#view_2").css("display", "none");
         $("#view_3").css("display", "none");
         $(this).find("span", 0).addClass("current");
         $("#view_by_2").find("span", 0).removeClass("current");
         $("#view_by_3").find("span", 0).removeClass("current");
         $(".filter-cond").css("display", "none");
         view = 0;
     }
     else
     {
         $("#view_1").css("display", "block");
         $("#view_2").css("display", "none");
         $("#view_3").css("display", "none");
         $(this).find("span", 0).addClass("current");
         $("#view_by_2").find("span", 0).removeClass("current");
         $("#view_by_3").find("span", 0).removeClass("current");
         $(".filter-cond").css("display", "block");
         view = 1;
     }
});
$("#view_by_2").bind('click', function(){
    if (view == 2)
    {
        $("#view_1").css("display", "none");
        $("#view_2").css("display", "none");
        $("#view_3").css("display", "none");
        $(this).find("span", 0).addClass("current");
        $("#view_by_2").find("span", 0).removeClass("current");
        $("#view_by_3").find("span", 0).removeClass("current");
        $(".filter-cond").css("display", "none");
        view = 0;
    }
    else
    {
        $("#view_2").css("display", "block");
        $("#view_1").css("display", "none");
        $("#view_3").css("display", "none");
        $(this).find("span", 0).addClass("current");
        $("#view_by_1").find("span", 0).removeClass("current");
        $("#view_by_3").find("span", 0).removeClass("current");
        $(".filter-cond").css("display", "block");
        view = 2;
    }
});
$("#view_by_3").bind('click', function(){
    if (view == 3)
    {
        $("#view_1").css("display", "none");
        $("#view_2").css("display", "none");
        $("#view_3").css("display", "none");
        $(this).find("span", 0).addClass("current");
        $("#view_by_2").find("span", 0).removeClass("current");
        $("#view_by_3").find("span", 0).removeClass("current");
        $(".filter-cond").css("display", "none");
        view = 0;
    }
    else
    {
        $("#view_3").css("display", "block");
        $("#view_1").css("display", "none");
        $("#view_2").css("display", "none");
        $(this).find("span", 0).addClass("current");
        $("#view_by_1").find("span", 0).removeClass("current");
        $("#view_by_2").find("span", 0).removeClass("current");
        $(".filter-cond").css("display", "block");
        view = 3;
    }
});