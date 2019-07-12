define(['core', 'tpl', './face.js'], function(core, tpl, face) {
	var modal = {};
	modal.init = function() {
		$('.post-func .icon').click(function() {
			$('.post-func .icon').removeClass('selected');
			$(".post-face").hide();
			$(".post-image").hide();
			$(this).addClass('selected');
			if ($(this).hasClass('icon-emoji')) {
				$(".post-face").show();
			} else if ($(this).hasClass('icon-pic')) {
				$(".post-image").show();
			}
		});
		face.init({
			class: '.post-face .item',
			input: $('#content')
		});

		// 发布动态
		$('#btnSend').click(function() {
			if ($(this).attr('stop')) {
				return;
			}
			if ($('#content').isEmpty()) {
				FoxUI.toast.show('说点什么吧~');
				return;
			}
			var images = [];
			$('#cell-images').find('li').each(function() {
				images.push($(this).data('filename'));
			});
			$(this).attr('stop', 1);
			core.json('/feed/api/create/', {
				content: $('#content').val(),
				images: images
			}, function(ret) {
				$('#btnSend').removeAttr('stop');
				if (ret.status == 0) {
					FoxUI.toast.show(ret.result.message);
					return;
				}
				var msg ='发表成功!';
				FoxUI.alert(msg, '提示', function() {
					if (ret.result.checked == '1' || $('.post-card').length <= 0) {
						$('.empty').hide();
						$('.container').html('');
						$('.infinite-loading').show();
					}
					$.router.back();
				});
			}, true, true);
		});

		modal.bindPostEvents();
	};

	modal.detail = function() {
        var to_comment = 0;
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i<vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == "to_comment") {
                to_comment = pair[1];
                break;
            }
        }
        // 滚动到评论视角
        if (to_comment == 1) {
            document.getElementById("comment_list").scrollIntoView();
        }

        $('#follow').click(function() {
            var follow_user_id = $(this).attr("attr");

            core.json('/follow/api/do/', {
                id: follow_user_id
            }, function(ret) {
                $('#btnSend').removeAttr('stop');
                if (ret.code == -1) {
                    FoxUI.toast.show(ret.message);
                    return;
                }

                if (ret.result.relation == 1 || ret.result.relation == 2) {
                    $('#follow').text("已关注").addClass("disabled");
                } else {
                    $('#follow').text("关注").removeClass("disabled");
                }
            }, true, true);
       });

        $('#like').click(function() {
            var follow_user_id = $(this).attr("attr");

            core.json('/like/api/do/', {
                targetId: follow_user_id
            }, function(ret) {
                $('#btnSend').removeAttr('stop');
                if (ret.code == -1) {
                    FoxUI.toast.show(ret.message);
                    return;
                }

                if (ret.result.liked == true) {
                    $('#like').find("span").removeClass("icon-like").addClass("icon-likefill");
                }
            }, true, true);
        });


	}

	modal.bindPostEvents = function() {
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('/common/upload/upload/'),
			removeUrl: core.getUrl('/common/upload/remove/'),
			imageCss: 'image-md'
		});
	};

	return modal;
});