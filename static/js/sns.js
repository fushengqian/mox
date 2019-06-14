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
		$('#btnSend').click(function() {
			if ($(this).attr('stop')) {
				return
			}
			if ($('#title').isEmpty()) {
				FoxUI.toast.show('标题没有填写哦~');
				return
			}
			if ($('#content').isEmpty()) {
				FoxUI.toast.show('说点什么吧~');
				return
			}
			var images = [];
			$('#cell-images').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			$(this).attr('stop', 1);
			core.json('sns/post/submit', {
				bid: modal.bid,
				title: $("#title").val(),
				content: $('#content').val(),
				images: images
			}, function(ret) {
				$('#btnSend').removeAttr('stop');
				if (ret.status == 0) {
					FoxUI.toast.show(ret.result.message);
					return
				}
				var msg = ret.result.checked == '1' ? '发表成功!' : '发表成功，请等待审核!';
				FoxUI.alert(msg, '提示', function() {
					if (ret.result.checked == '1' || $('.post-card').length <= 0) {
						$('.empty').hide();
						$('.container').html('');
						$('.infinite-loading').show();
						modal.page = 1;
						modal.getList()
					}
					$.router.back()
				})
			}, true, true)
		});
		modal.bindPostEvents();
	};

	modal.bindPostEvents = function() {
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('/common/upload/do/'),
			removeUrl: core.getUrl('/common/upload/remove/'),
			imageCss: 'image-md'
		})
	};

	return modal;
});