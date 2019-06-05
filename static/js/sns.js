define(['core', 'tpl', './face.js'], function(core, tpl, face) {
	var modal = {
		page: 1,
		bid: 0,
		cid: 0,
		keywords: '',
		listpage: 1
	};
	modal.init = function(params) {
		modal.textShow();
		if (params.page != '0') {
			modal.page = params.page
		}
		if (modal.bid != params.bid) {
			modal.page = 1
		}
		modal.bid = params.bid;
		$('#btnFollow').click(function() {
			var isfollow = $(this).data('follow') == '1';
			core.json('sns/board/follow', {
				bid: modal.bid
			}, function(ret) {
				if (ret.status == 0) {
					FoxUI.toast.show(ret.result.message);
					return
				}
				var isfollow = ret.result.isfollow;
				if (isfollow) {
					$('#btnFollow').html('<i class="icon icon-check"></i> 已关注').removeClass('btn-warning').addClass('btn-default')
				} else {
					$('#btnFollow').html('<i class="icon icon-add"></i> 关注').removeClass('btn-default').addClass('btn-warning')
				}
				$('#btnFollow').removeAttr('stop')
			}, true, true)
		});
		$('#btnNeedFollow').click(function() {
			var followurl = $(this).data('followurl');
			FoxUI.alert(params.followtip, '提示', function() {
				location.href = followurl
			})
		});
		$('.fui-content').infinite({
			onLoading: function() {
				modal.getList()
			}
		});
		if (modal.page == 1) {
			if ($(".post-card").length <= 0) {
				modal.getList()
			} else {
				modal.page++
			}
		}
		$('.post-func .icon').click(function() {
			$('.post-func .icon').removeClass('selected');
			$(".post-face").hide();
			$(".post-image").hide();
			$(this).addClass('selected');
			if ($(this).hasClass('icon-emoji')) {
				$(".post-face").show()
			} else if ($(this).hasClass('icon-pic')) {
				$(".post-image").show()
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
		modal.bindEvents()
	};
	modal.complain = function() {
		$("#complain-pic").on("click", function() {
			if ($(".complain-image").css("display") == "none") {
				$(".complain-image").show()
			} else {
				$(".complain-image").hide()
			}
		});
		$(".complain-type a").on("click", function() {
			if ($(this).hasClass("active")) {
				return
			} else {
				$(".complain-type a").removeClass("active");
				$(this).addClass("active");
				$("#complain_type").val($(this).attr("data-type"))
			}
		});
		$('#btnCompSend').click(function() {
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
			$('.complain-image').find('li').each(function() {
				images.push($(this).data('filename'))
			});
			$(this).attr('stop', 1);
			core.json('sns/post/complain', {
				id: modal.postid,
				type: modal.type,
				content: $('#complain_text').val(),
				images: images
			}, function(ret) {
				if (ret.status == 0) {
					$('#btnSend').removeAttr('stop');
					FoxUI.toast.show(ret.result.message);
					return
				} else {
					var msg = ret.result.checked == '1' ? '投诉提交成功!' : '投诉提交成功，请等待审核!';
					FoxUI.alert(msg, '提示', function() {
						if (ret.result.checked == '1' || $('.reply-list-group .reply-list').length <= 0) {
							$('.empty').hide();
							$('.reply-list-group').html('');
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
				}
			}, true, true)
		})
	};
	modal.getList = function() {
		core.json('sns/board/getlist', {
			page: modal.page,
			bid: modal.bid
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('.board-list-empty').show();
				$('.fui-content').infinite('stop')
			} else {
				$('.board-list-empty').hide();
				$('.fui-content').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-content').infinite('stop')
				}
			}
			modal.page++;
			core.tpl('.container', 'tpl_board_post_list', result, modal.page > 1);
			modal.bindEvents();
			modal.textShow();
			modal.link_complain();
			modal.complain()
		})
	};
	modal.link_complain = function() {
		$(document).off("click", ".link-complain").on("click", ".link-complain", function() {
			modal.postid = $(this).attr("data-id");
			core.json('sns/post/checkPost', {
				postid: modal.postid
			}, function(ret) {
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
					$.router.load('#sns-board-complain-page')
				}
			})
		})
	};
	modal.textShow = function() {
		$(".sns-content-info").css({
			"-webkit-box-orient": "",
			"-webkit-line-clamp": "",
			"display": "",
			"max-height": "4rem"
		});
		$(".sns-card-show").html("全文");
		$(".sns-card-list").each(function() {
			$(this).find(".sns-card-show").hide();
			var $divHeight = $(this).find(".sns-content-info");
			var $divHeightSub = $(this).find(".sns-content-info-sub");
			var dheight = $divHeight.height();
			var dheightSub = $divHeightSub.height();
			if (dheightSub > dheight) {
				$(this).find(".sns-card-show").show();
				var text = $(this).find(".sns-card-show").html();
				$divHeight.css({
					"-webkit-box-orient": "vertical",
					"-webkit-line-clamp": "4",
					"display": "-webkit-box",
				});
				$(document).on("click", ".sns-card-show", function() {
					if (text == "全文") {
						$divHeight.css({
							"-webkit-box-orient": "",
							"-webkit-line-clamp": "",
							"display": "",
							"max-height": "" + dheightSub + "px"
						});
						$(this).html("收起");
						text = "收起"
					} else if (text == "收起") {
						$divHeight.css({
							"-webkit-box-orient": "vertical",
							"-webkit-line-clamp": "4",
							"display": "-webkit-box",
							"max-height": "" + dheight + "px"
						});
						$(this).html("全文");
						text = "全文"
					}
				})
			}
		})
	};
	modal.bindEvents = function() {
		$('.like-good').unbind('click').click(function() {
			var link = $(this);
			if ($(this).attr('stop')) {
				return
			}
			var pid = $(this).data('pid');
			$(this).attr('stop');
			core.json('sns/post/like', {
				bid: modal.bid,
				pid: pid
			}, function(ret) {
				if (ret.status == 0) {
					link.removeAttr('stop');
					FoxUI.toast.show(ret.result.message);
					return
				}
				link.removeAttr('stop');
				$(".like-good[data-pid='" + pid + "'] span").html(ret.result.good)
			}, true, true)
		})
	};
	modal.bindPostEvents = function() {
		$('.fui-uploader').uploader({
			uploadUrl: core.getUrl('util/uploader'),
			removeUrl: core.getUrl('util/uploader/remove'),
			imageCss: 'image-md'
		})
	};
	modal.initList = function(params) {
		modal.cid = params.cid;
		if (params.page != '0') {
			modal.listpage = params.page
		}
		$('.fui-fullHigh-item').infinite({
			onLoading: function() {
				modal.getBoardList()
			}
		});
		if (modal.listpage == 1) {
			if ($('.board-lists-item').length <= 0) {
				modal.getBoardList()
			} else {
				modal.listpage++
			}
		}
		$('#tab nav').click(function() {
			$('#board-list-group').hide();
			$(this).siblings().removeClass("on");
			$(this).addClass("on");
			$('.title').html($(this).html());
			$('.infinite-loading').show();
			$('#board-list-group').html('');
			modal.listpage = 1, modal.cid = $(this).data('cid'), modal.keywords = '';
			modal.getBoardList()
		});
		$('form').submit(function() {
			$('.infinite-loading').show();
			$('#board-list-group').html('');
			modal.listpage = 1;
			modal.cid = '';
			modal.keywords = $('#keywords').val();
			modal.getBoardList();
			return false
		})
	};
	modal.getBoardList = function() {
		core.json('sns/board/get_boardlist', {
			page: modal.listpage,
			cid: modal.cid,
			keywords: modal.keywords
		}, function(ret) {
			var result = ret.result;
			if (result.total <= 0) {
				$('#board-list-group').hide();
				$('.empty').show();
				$('.fui-fullHigh-item').infinite('stop');
				$('.board-list-group').hide()
			} else {
				$('#board-list-group').show();
				$('.empty').hide();
				$('.fui-fullHigh-item').infinite('init');
				if (result.list.length <= 0 || result.list.length < result.pagesize) {
					$('.fui-fullHigh-item').infinite('stop')
				}
			}
			modal.listpage++;
			core.tpl('#board-list-group', 'tpl_board_lists', result, modal.listpage > 1)
		})
	};
	return modal
});