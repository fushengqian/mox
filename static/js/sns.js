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
			core.json('feed/api/create/', {
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

	modal.bindPostEvents = function() {
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('/common/upload/upload/'),
			removeUrl: core.getUrl('/common/upload/remove/'),
			imageCss: 'image-md'
		});
	};

	return modal;
});