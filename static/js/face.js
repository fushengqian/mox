define(['core', 'tpl'], function(core, tpl) {
	var modal = {};
	modal.init = function(params) {
		$.extend({
			browser: function() {
				var rwebkit = /(webkit)\/([\w.]+)/,
					ropera = /(opera)(?:.*version)?[ \/]([\w.]+)/,
					rmsie = /(msie) ([\w.]+)/,
					rmozilla = /(mozilla)(?:.*? rv:([\w.]+))?/,
					browser = {},
					ua = window.navigator.userAgent,
					browserMatch = uaMatch(ua);
				if (browserMatch.browser) {
					browser[browserMatch.browser] = true;
					browser.version = browserMatch.version
				}
				return {
					browser: browser
				}
			},
		});

		function uaMatch(ua) {
			ua = ua.toLowerCase();
			var match = rwebkit.exec(ua) || ropera.exec(ua) || rmsie.exec(ua) || ua.indexOf("compatible") < 0 && rmozilla.exec(ua) || [];
			return {
				browser: match[1] || "",
				version: match[2] || "0"
			}
		}
		$.extend({
			unselectContents: function() {
				if (window.getSelection) window.getSelection().removeAllRanges();
				else if (document.selection) document.selection.empty()
			}
		});
		$.fn.extend({
			selectContents: function() {
				$(this).each(function(i) {
					var node = this;
					var selection, range, doc, win;
					if ((doc = node.ownerDocument) && (win = doc.defaultView) && typeof win.getSelection != 'undefined' && typeof doc.createRange != 'undefined' && (selection = window.getSelection()) && typeof selection.removeAllRanges != 'undefined') {
						range = doc.createRange();
						range.selectNode(node);
						if (i == 0) {
							selection.removeAllRanges()
						}
						selection.addRange(range)
					} else if (document.body && typeof document.body.createTextRange != 'undefined' && (range = document.body.createTextRange())) {
						range.moveToElementText(node);
						range.select()
					}
				})
			},
			setCaret: function() {
				if (!$.browser.msie) return;
				var initSetCaret = function() {
						var textObj = $(this).get(0);
						textObj.caretPos = document.selection.createRange().duplicate()
					};
				$(this).click(initSetCaret).select(initSetCaret).keyup(initSetCaret)
			},
			insertAtCaret: function(textFeildValue) {
				var textObj = $(this).get(0);
				if (document.all && textObj.createTextRange && textObj.caretPos) {
					var caretPos = textObj.caretPos;
					caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? textFeildValue + '' : textFeildValue
				} else if (textObj.setSelectionRange) {
					var rangeStart = textObj.selectionStart;
					var rangeEnd = textObj.selectionEnd;
					var tempStr1 = textObj.value.substring(0, rangeStart);
					var tempStr2 = textObj.value.substring(rangeEnd);
					textObj.value = tempStr1 + textFeildValue + tempStr2;
					textObj.focus();
					var len = textFeildValue.length;
					textObj.setSelectionRange(rangeStart + len, rangeStart + len);
					textObj.blur()
				} else {
					textObj.value += textFeildValue
				}
			}
		});
		$(params.class).click(function() {
			var face = "[EM" + $(this).data('face') + "]";
			params.input.setCaret();
			params.input.insertAtCaret(face)
		})
	};
	return modal
});