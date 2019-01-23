$(document).ready(function(){
    //评论表情初始化
    $('.comment-face').click(function(){
        var target_id = $(this).attr("attr");
        var content = $("#comment_area_"+target_id).html();
        if ( content.indexOf('发表你的评论...') > -1) {
            var content = content.replace('发表你的评论...', '');
            $("#comment_area_"+target_id).html(content);
        }

        $("#comment_area_"+target_id).emoji({
            button: "#comment_btn_"+target_id,
            showTab: false,
            animation: 'slide',
            icons: [{
                name: "QQ表情",
                path: "/static/emoji/qq/",
                maxNum: 91,
                excludeNums: [41, 45, 54],
                file: ".gif"
            }]
        });
    });

    // 发布动态表情初始化
    $(".editor").emoji({
        button: ".show_emoji",
        showTab: false,
        animation: 'slide',
        icons: [{
            name: "QQ表情",
            path: "/static/emoji/qq/",
            maxNum: 91,
            excludeNums: [41, 45, 54],
            file: ".gif"
        }]
    });

    //点赞
    $('.icon-good').click(function(){
        var target_id = $(this).attr("attr");
        var target = $(this);
        $.ajax({
            type: "post",
            url: "/like/like/",
            data: {target_id:target_id},
            dataType: "json",
            success: function(data){
                if (data.errno == '1') {
                    var num = target.find('.num', 0).text();
                    target.find('.num', 0).text(parseInt(num)+1);
                }
            }
        });
    });

    // 评论
    $('.icon-comment').click(function(){
        var target_id = $(this).attr("attr");
        if ($("#comment_" + target_id).hasClass("hide")) {
            // 显示
            $("#comment_" + target_id).removeClass("hide");
            $('.user-attr').css('position', 'static');
        } else {
            // 隐藏
            $("#comment_" + target_id).addClass("hide");
            $('.user-attr').css('position', 'absolute');
        }
    });

    $(".comment-area").click(function(){
        var text = $(this).html();
        if ( text.indexOf('发表你的评论...') > -1) {
            var html = text.replace('发表你的评论...', '');
            $(this).html(html);
        }
    });

    // 提交评论
    $(".send-comment").click(function(){
        var target_id = $(this).attr("attr");
        var target = $(this);
        var content = target.parent().find('.comment-area', 0).html();

        if ( content.indexOf('发表你的评论...') > -1) {
            var content = content.replace('发表你的评论...', '');
            $("#comment_area_"+target_id).html(content);
        }

        $.ajax({
            type: "post",
            url: "/comment/comment/",
            data: {target_id:target_id, content:content},
            dataType: "json",
            success: function(data){
                if (data.errno == '1') {
                    var html = '<div class="comment-item">'+
                                     '<div class="user-info">'+
                                           '<img src="'+data.rsm.user_info.avatar+'"/>'+
                                           '<span class="nick">'+data.rsm.user_info.user_name+'</span>'+
                                           '<span class="time">刚刚</span>'+
                                     '</div>'+
                                    '<div class="comment-desc">'+data.rsm.comment+'</div>'+
                               '</div>';
                    $("#comment_"+target_id).find(".comments", 0).append(html);
                    target.parent().find('.comment-area', 0).text("发表你的评论...");
                } else {
                    alert(data.err);
                }
            }
        });
    });

    // 搜索
    $('.btn-search').click(function(){
        var keyword = $(this).parent().find('input', 0).val();
        if (keyword.length > 0) {
            return;
        }
    });

    // 插入话题
    $('.insert-topic').click(function(){
        var html = '<span id="topic-o">#<span id="topic-j">在这里输入您的话题</span>#</span>';
        var ed = $('.editor').html();
        if (ed.indexOf("#") <= 0) {
            $('.editor').prepend(html);
        }
        $('#topic-o').click(function(){
            $('#topic-j').html("");
        });
    });
});