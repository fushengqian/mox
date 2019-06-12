define(['core', 'tpl', './face.js'], function (core, tpl, face) {
    var modal = {
        page: 1,
        bid: 0,
        pid: 0,
        rpid: 0
    };
    modal.init = function (params) {
        modal.page = 1;
        modal.bid = params.bid;
        modal.pid = params.pid;
        $('.fui-content').infinite({
            onLoading: function () {
                modal.getList()
            }
        });
        if (modal.page == 1) {
            if ($(".reply-list").length <= 0) {
                modal.getList()
            } else {
                modal.page++
            }
        }
        $('.fui-uploader').uploader({
            uploadUrl: core.getUrl('util/uploader'),
            removeUrl: core.getUrl('util/uploader/remove')
        });
        $('#btnDelete').click(function () {
            FoxUI.confirm('确认删除此话题?', function () {
                core.json('sns/post/delete', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    location.href = core.getUrl('sns/board', {
                        id: modal.bid
                    })
                }, true, true)
            })
        });
        $('#btnCheck').click(function () {
            FoxUI.confirm('确认审核此话题?', function () {
                core.json('sns/post/check', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    FoxUI.toast.show('审核成功!');
                    $('#btnCheck').remove()
                }, true, true)
            })
        });
        $('#btnBest').click(function () {
            var btn = $(this);
            var isbest = btn.attr('isbest');
            var msg = isbest == '1' ? '取消精华' : '设置精华';
            FoxUI.confirm('确认' + msg + '?', function () {
                core.json('sns/post/best', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    if (ret.result.isbest == '1') {
                        btn.find('.bestdiv').html('取消精华');
                        $('#bestspan').show()
                    } else {
                        btn.find('.bestdiv').html('设置精华');
                        $('#bestspan').hide()
                    }
                    FoxUI.toast.show('设置成功');
                    btn.attr('isbest', ret.result.isbest)
                }, true, true)
            })
        });
        $('#btnTop').click(function () {
            var btn = $(this);
            var isbest = btn.attr('istop');
            var msg = isbest == '1' ? '取消置顶' : '设置置顶';
            FoxUI.confirm('确认' + msg + '?', function () {
                core.json('sns/post/top', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    if (ret.result.istop == '1') {
                        btn.find('.topdiv').html('取消置顶');
                        $('#bestspan').show()
                    } else {
                        btn.find('.topdiv').html('设置置顶');
                        $('#bestspan').hide()
                    }
                    FoxUI.toast.show('设置成功');
                    btn.attr('istop', ret.result.isbest)
                }, true, true)
            })
        });
        $('#btnBestAll').click(function () {
            var btn = $(this);
            var isbest = btn.attr('isbest');
            var msg = isbest == '1' ? '取消全站精华' : '设置全站精华';
            FoxUI.confirm('确认' + msg + '?', function () {
                core.json('sns/post/allbest', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    if (ret.result.isbest == '1') {
                        btn.find('.bestdiv').html('取消全站精华');
                        $('#bestspan').show()
                    } else {
                        btn.find('.bestdiv').html('设置全站精华');
                        $('#bestspan').hide()
                    }
                    FoxUI.toast.show('设置成功');
                    btn.attr('isbest', ret.result.isbest)
                }, true, true)
            })
        });
        $('#btnTopAll').click(function () {
            var btn = $(this);
            var isbest = btn.attr('istop');
            var msg = isbest == '1' ? '取消全站置顶' : '设置全站置顶';
            FoxUI.confirm('确认' + msg + '?', function () {
                core.json('sns/post/alltop', {
                    bid: modal.bid,
                    pid: modal.pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    if (ret.result.istop == '1') {
                        btn.find('.topdiv').html('取消全站置顶');
                        $('#bestspan').show()
                    } else {
                        btn.find('.topdiv').html('设置全站置顶');
                        $('#bestspan').hide()
                    }
                    FoxUI.toast.show('设置成功');
                    btn.attr('istop', ret.result.isbest)
                }, true, true)
            })
        });
        core.showImages('.fui-article-content img');
        $('#btnReply').click(function () {
            modal.rpid = 0;
            $('#content').attr('placeholder', '内容 10-1000个字');
            $.router.load('#sns-board-reply-page')
        });
        $('#btnGood').click(function () {
            var btn = $(this);
            core.json('sns/post/like', {
                bid: modal.bid,
                pid: modal.pid,
                isgood: btn.attr('isgood') == '1' ? 0 : 1
            }, function (ret) {
                if (ret.status == 0) {
                    $('#btnGood').removeAttr('stop');
                    FoxUI.toast.show(ret.result.message);
                    return
                }
                if (ret.result.isgood == '1') {
                    btn.find('.icon').removeClass('icon-appreciate icon-appreciatefill').addClass('icon-appreciatefill')
                } else {
                    btn.find('.icon').removeClass('icon-appreciate icon-appreciatefill').addClass('icon-appreciate')
                }
                btn.attr('isgood', ret.result.isgood);
                if (ret.result.good > 0) {
                    btn.find('.zandiv').html(ret.result.good)
                } else {
                    btn.find('.zandiv').html('赞')
                }
            }, false, true)
        });
        $('.post-func .icon').click(function () {
            $('.post-func .icon').removeClass('selected');
            $(".post-face").hide();
            $(".post-image").hide();
            $(this).addClass('selected');
            if ($(this).hasClass('icon-emoji')) {
                $(".post-face").show()
            } else if ($(this).hasClass('icon-pic')) {
                $(".post-image").show();
                $(".fui-images").html('')
            }
        });
        face.init({
            class: '.post-face .item',
            input: $('#content')
        });
        $('#btnSend').click(function () {
            if ($(this).attr('stop')) {
                return
            }
            if ($('#content').isEmpty()) {
                FoxUI.toast.show('说点什么吧~');
                return
            }
            var images = [];
            $('.post-image').find('li').each(function () {
                images.push($(this).data('filename'))
            });
            $(this).attr('stop', 1);
            core.json('sns/post/reply', {
                bid: modal.bid,
                pid: modal.pid,
                rpid: modal.rpid,
                content: $('#content').val(),
                images: images
            }, function (ret) {
                if (ret.status == 0) {
                    $('#btnSend').removeAttr('stop');
                    FoxUI.toast.show(ret.result.message);
                    return
                }
                var msg = ret.result.checked == '1' ? '评论成功!' : '评论成功，请等待审核!';
                FoxUI.alert(msg, '提示', function () {
                    if (ret.result.checked == '1' || $('.reply-list-group .reply-list').length <= 0) {
                        $('.empty').hide(),
                            $('.reply-list-group').html(''),
                            $('.infinite-loading').show();
                        modal.page = 1;
                        modal.getList()
                    }
                    $('#content').val('');
                    $('.post-func .icon').removeClass('selected');
                    $(".post-face").hide();
                    $(".post-image").hide();
                    $('#btnSend').removeAttr('stop');
                    $.router.back()
                })
            }, true, true)
        });
        $('#btnComplain').click(function () {
            modal.postid = $("#btnComplain").attr("data-id");
            core.json('sns/post/checkPost', {
                postid: modal.postid
            }, function (ret) {
                if (ret.status > 0) {
                    var result = ret.result,
                        type = '';
                    console.log(result);
                    if (result.post.pid == 0) {
                        type = '话题'
                    } else {
                        type = '评论'
                    }
                    $("#complain_type").val('');
                    $(".complain-type a").removeClass("active");
                    $('#complain_text').val('');
                    $(".complain-image ul").html('');
                    $('#complain_text').attr('placeholder', '内容 10-1000个字');
                    $("#post_member").html(" " + result.post.nickname + " ");
                    $(".complain-type-span").html(type);
                    $('#btnCompSend').removeAttr('stop');
                    $.router.load('#sns-board-complain-page');
                    modal.complain()
                }
            })
        });
        $('#btnCompSend').click(function () {
            if ($(this).attr('stop')) {
                return
            }
            modal.type = $('#complain_type').val();
            if (modal.type == '') {
                FoxUI.toast.show('请选择投诉类别~');
                return
            }
            if ($('#complain_text').isEmpty()) {
                FoxUI.toast.show('说点什么吧~');
                return
            }
            var images = [];
            $('.complain-image').find('li').each(function () {
                images.push($(this).data('filename'))
            });
            $(this).attr('stop', 1);
            core.json('sns/post/complain', {
                id: modal.postid,
                type: modal.type,
                content: $('#complain_text').val(),
                images: images
            }, function (ret) {
                if (ret.status == 0) {
                    $('#btnSend').removeAttr('stop');
                    FoxUI.toast.show(ret.result.message);
                    return
                } else {
                    var msg = ret.result.checked == '1' ? '投诉提交成功!' : '投诉提交成功，请等待审核!';
                    FoxUI.alert(msg, '提示', function () {
                        $('#content').val('');
                        $('.post-func .icon').removeClass('selected');
                        $(".post-face").hide();
                        $(".post-image").hide();
                        $('#btnSend').removeAttr('stop');
                        $.router.back()
                    })
                }
            }, true, true)
        })
    };
    modal.getList = function () {
        core.json('sns/post/getlist', {
            page: modal.page,
            bid: modal.bid,
            pid: modal.pid
        }, function (ret) {
            var result = ret.result;
            if (result.total <= 0) {
                $('.reply-list-group').hide();
                $('.empty').show();
                $('.fui-content').infinite('stop')
            } else {
                $('.reply-list-group').show();
                $('.empty').hide();
                $('.fui-content').infinite('init');
                if (result.list.length <= 0 || result.list.length < result.pagesize) {
                    $('.fui-content').infinite('stop')
                }
            }
            modal.page++;
            core.tpl('.reply-list-group', 'tpl_post_reply_list', result, modal.page > 1);
            modal.bindReplyEvents();
            modal.complain();
            modal.link_complain()
        })
    };
    modal.complain = function () {
        $(".complain-type a").on("click", function () {
            if ($(this).hasClass("active")) {
                return
            } else {
                $(".complain-type a").removeClass("active");
                $(this).addClass("active");
                $("#complain_type").val($(this).attr("data-type"))
            }
        })
    };
    modal.link_complain = function () {
        $(".link-complain-list").on("click", function () {
            modal.postid = $(this).attr("data-id");
            core.json('sns/post/checkPost', {
                postid: modal.postid
            }, function (ret) {
                if (ret.status > 0) {
                    var result = ret.result,
                        type = '';
                    console.log(result);
                    if (result.post.pid == 0) {
                        type = '话题'
                    } else {
                        type = '评论'
                    }
                    $("#complain_type").val('');
                    $(".complain-type a").removeClass("active");
                    $('#complain_text').val('');
                    $(".complain-image ul").html('');
                    $('#complain_text').attr('placeholder', '内容 10-1000个字');
                    $("#post_member").html(" " + result.post.nickname + " ");
                    $(".complain-type-span").html(type);
                    $('#btnCompSend').removeAttr('stop');
                    $.router.load('#sns-board-complain-page')
                }
            })
        })
    };
    modal.bindReplyEvents = function () {
        $('.reply-list .link-reply').unbind('click').click(function () {
            var item = $(this).closest('.reply-list');
            var pid = item.data('pid');
            var nickname = item.data('nickname');
            modal.rpid = pid;
            $('#content').attr('placeholder', '回复: ' + nickname);
            $.router.load('#sns-board-reply-page')
        });
        $('.reply-list .link-check').unbind('click').click(function () {
            var item = $(this).closest('.reply-list');
            var pid = item.data('pid');
            FoxUI.confirm('确认审核此回复?', function () {
                core.json('sns/post/check', {
                    bid: modal.bid,
                    pid: pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    FoxUI.toast.show('审核成功!');
                    $(this).remove()
                }, true, true)
            })
        });
        $('.reply-list .link-delete').unbind('click').click(function () {
            var item = $(this).closest('.reply-list');
            var pid = item.data('pid');
            FoxUI.confirm('确认删除此回复?', function () {
                core.json('sns/post/delete', {
                    bid: modal.bid,
                    pid: pid
                }, function (ret) {
                    if (ret.status == 0) {
                        FoxUI.toast.show(ret.result.message);
                        return
                    }
                    FoxUI.toast.show('删除成功!');
                    item.remove();
                    if ($('.reply-list').length <= 0) {
                        $('.reply-list-group').hide();
                        $('.empty').show()
                    }
                }, true, true)
            })
        });
        $('.reply-list .link-good').unbind('click').click(function (e) {
            var link = $(this);
            var item = link.closest('.reply-list');
            var pid = item.data('pid');
            core.json('sns/post/like', {
                bid: modal.bid,
                pid: pid,
                isgood: link.attr('isgood') == '1' ? 0 : 1
            }, function (ret) {
                if (ret.status == 0) {
                    link.removeAttr('stop');
                    FoxUI.toast.show(ret.result.message);
                    return
                }
                if (ret.result.isgood == '1') {
                    link.find('.icon').removeClass('icon-appreciate icon-appreciatefill').addClass('icon-appreciatefill')
                } else {
                    link.find('.icon').removeClass('icon-appreciate icon-appreciatefill').addClass('icon-appreciate')
                }
                if (ret.result.good > 0) {
                    link.find('.zandiv').html(ret.result.good)
                } else {
                    link.find('.zandiv').html('赞')
                }
                link.removeAttr('stop')
            }, false, true)
        });
        core.showImages('.reply-list .fui-card-images img')
    };
    return modal
});