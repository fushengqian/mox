define(['core', 'tpl'], function (core, tpl) {
    var modal = {
        page: 2,
    };

    modal.init = function () {
        $('.fui-content').infinite({
            onLoading: function() {
                modal.getList();
            }
        });

        if (modal.page == 1) {
            modal.getList();
        }

        // tab切换
        $('.feed-tab').click(function(){
            var tab = $(this).attr("attr");
            FoxUI.toast.show(tab);
        });
    }

    modal.loading = function () {
        modal.page++;
    };

    modal.getList = function () {
        core.json('/feed/api/list/', {
            page: modal.page
        }, function(ret) {
            modal.page++;
            var data = ret.result;
            core.tpl('.feed-list', 'tpl_feed_list', data, modal.page > 1);
            $('.fui-content').infinite('init');

            modal.bindEvents();
        });
    };

    modal.bindEvents = function () {
        // 点赞
        $(".icon-like").click(function () {
            if (isLogin != '1') {
                FoxUI.alert('请先登录哦~', '提示', function(){
                    window.location.href = '/user/login/';
                });
                return;
            }
            // 赞数+1
            if ($(this).attr('islike') != '1'){
                $(this).css('color', '#ff6600');
                $(this).attr("islike", '1');
                var feed_id = $(this).parent().attr("id");
                var like_num = parseInt($(this).find('.num', 0).text()) + 1;
                $(this).find('.num', 0).text(like_num);

                core.json('/like/api/do/', {
                    targetId: feed_id
                }, function() {
                    return;
                }, true, true);
            }
        });

        // 评论
        $(".icon-comment").click(function () {
            var feed_id = $(this).parent().attr("id");
            window.location.href = '/feed/'+feed_id+'?to_comment=1';
        });

        // 详情
        $(".feed-content").click(function () {
            var feed_id = $(this).attr("attr");
            window.location.href = '/feed/'+feed_id;
        });
    };

    modal.bindEvents();

    return modal;
});