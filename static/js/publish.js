$(document).ready(function(){ 
    //上传图片
    var upload = new MultipleUploader({
        trigger: $(".add-place"),
        action: '/common/ajax/upload_comment/',
        name: 'file',
        accept: 'image/*',
        multiple: true,
        progress: null,
    }).change(function (files) {
        if (user_id == 0)
        {
            $('#loginModal').modal();
            return;
        }
        this.submit();
    }).success(function (re) {
        var re = $.parseJSON(re);
        if (re.errno == 0)
        {
            var html = '<dd id="dd_'+re.rsm+'">'+
                       '<div class="place">'+
                       '<div class="img"><img src="'+re.err+'" style="width:120px;height:120px"></div>'+
                       '<div class="title"><h4 id="'+re.rsm+'"></h4></div>'+
                       '<input type="hidden" name="image[]" value="'+re.err+'">'+
                       '<input type="hidden" name="desc[]" id="brief_'+re.rsm+'">'+
                       '<div class="mask-operate"><a lang="'+re.rsm+'" class="btn-edit"></a><a lang="'+re.rsm+'" class="btn-remove"></a></div>'+
                       '</div>'+
                       '</dd>';
           
           $('.upload-box').append(html);
           $("#upload-preview").attr("src", re.err);
           $("#upload-preview").attr("lang", re.rsm);
           $('#myModal').modal();
        }
        else
        {
           alert(re.err);
        }
    });
    
    $("#add-brief").bind('click', function(){
        var brief = $("#brief_content").val();
        var rsm = $("#upload-preview").attr("lang");
        $("#brief_"+rsm).val(brief);
        $("#"+rsm).text(brief);
        $("#brief_content").val("");
        $('#myModal').modal("hide");
    });
    
    $(".upload-box").on("click", ".btn-edit", function () {
         $('#myModal').modal();
         var brief = $("#brief_"+$(this).attr("lang")).val();
         var url = $("#dd_"+$(this).attr("lang")).find("img", 0).attr("src");
         $("#upload-preview").attr("lang", $(this).attr("lang"));
         $("#upload-preview").attr("src", url);
         $("#brief_content").val(brief);
    });
    
    $(".upload-box").on("click", ".btn-remove", function () {
         $("#dd_"+$(this).attr("lang")).remove();
         var id = $(this).attr("id");
         $("#cur_img").val(id);
         FNC.ajax_post($('#removeimg'), FNC.ajax_processer, 'error_message');
    });
    
    $("#publish").bind("click", function(){
       if (parseInt(user_id) <= 0)
       {
             $('#loginModal').modal();
             return;
       }
       else
       {
             FNC.ajax_post($('#form'), FNC.ajax_processer, 'error_message');
       }
    });
    
    draw_map($("input[name='lng']").attr('value'), $("input[name='lat']").attr("value"));
});

var blue_point = "/static/blue_point.png";
var red_point = "/static/red_point.png";

//查找地图
$("#searchmap").bind('click', function(){
   var address = $("#map_address").val();
   if (address.length < 1)
   {
       if ($("#address").val().length > 1)
       {
           address = $("#address").val();
           $("#map_address").val(address);
       }
       else
       {
           alert('查找地址不能为空！');
       }
   }
   var city = '';
   search_api(address, city);
});

//修改地图
$("#modifymap").bind('click', function(){
    var lat = $("input[name='lat']").attr('value');
    var lng = $("input[name='lng']").attr('value');
    editMap(lng, lat);
});

//取消修改
$("#cancel_btn").bind("click",function(){
    $("#container_front").hide();
});