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
    }

    modal.loading = function () {
        modal.page++;
    };

    modal.getList = function () {
        core.json('feed/api/list/', {
            page: modal.page
        }, function(ret) {
            modal.page++;
            var data = ret.result;
            core.tpl('.feed-list', 'tpl_feed_list', data, modal.page > 1);
            $('.fui-content').infinite('init');
        });
    };
    return modal;
});