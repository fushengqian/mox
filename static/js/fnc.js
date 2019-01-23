var FNC =
{
    //全局loading
    loading: function (type)
    {
        if (!$('#fm-loading').length)
        {
            $('.fm-ajax-box').append(FNC_TEMPLATE.loadingBox);
        }
        
        if (type == 'show')
        {
            if ($('#fm-loading').css('display') == 'block')
            {
                return false;
            }
            
            $('#fm-loading').fadeIn();
            
            FNC.G.loading_timer = setInterval(function ()
            {
                FNC.G.loading_bg_count -= 1;
                
                $('#fm-loading-box').css('background-position', '0px ' + FNC.G.loading_bg_count * 40 + 'px');
                
                if (FNC.G.loading_bg_count == 1)
                {
                    FNC.G.loading_bg_count = 12;
                }
            }, 100);
        }
        else
        {
            $('#fm-loading').fadeOut();
            clearInterval(FNC.G.loading_timer);
        }
    },
    
    loading_mini: function (selector, type)
    {
        if (!selector.find('#fm-loading-mini-box').length)
        {
            selector.append(FNC_TEMPLATE.loadingMiniBox);
        }
        
        if (type == 'show')
        {
            selector.find('#fm-loading-mini-box').fadeIn();
            
            FNC.G.loading_timer = setInterval(function ()
            {
                FNC.G.loading_mini_bg_count -= 1;
                
                $('#fm-loading-mini-box').css('background-position', '0px ' + FNC.G.loading_mini_bg_count * 16 + 'px');
                
                if (FNC.G.loading_mini_bg_count == 1)
                {
                    FNC.G.loading_mini_bg_count = 9;
                }
            }, 100);
        }
        else
        {
            selector.find('#fm-loading-mini-box').fadeOut();
            clearInterval(FNC.G.loading_timer);
        }
    },
    
    ajax_request: function(url, params)
    {
        FNC.loading('show');
        
        if (params)
        {
            $.post(url, params + '&_post_type=ajax', function (result)
            {
                _callback(result);
            }, 'json').error(function (error)
            {
                _error(error);
            });
        }
        else
        {
            $.get(url, function (result)
            {
                _callback(result);
            }, 'json').error(function (error)
            {
                _error(error);
            });
        }
        
        function _callback (result)
        {
            FNC.loading('hide');
            
            if (!result)
            {
                return false;
            }
            
            if (result.err)
            {
                //FNC.alert(result.err);
                alert(result.err);
            }
            else if (result.rsm && result.rsm.url)
            {
                window.location = decodeURIComponent(result.rsm.url);
            }
            else if (result.errno == 1)
            {
                window.location.reload();
            }
        }
        
        function _error (error)
        {
            FNC.loading('hide');
            
            if ($.trim(error.responseText) != '')
            {
                alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText);
            }
        }
        
        return false;
    },
    
    ajax_post: function(formEl, processer, type) // 表单对象，用 jQuery 获取，回调函数名
    {
        if (typeof (processer) != 'function')
        {
            var processer = FNC.ajax_processer;
            FNC.loading('show');
        }
        
        if (!type)
        {
            var type = 'default';
        }
        
        var custom_data = {
            _post_type: 'ajax'
        };
        
        formEl.ajaxSubmit(
        {
            dataType: 'json',
            data: custom_data,
            success: function (result)
            {
                processer(type, result);
            },
            error: function (error)
            {
                console.log(error);
                if ($.trim(error.responseText) != '')
                {
                    FNC.loading('hide');
                    alert(_t('发生错误, 返回的信息:') + ' ' + error.responseText);
                }
                else if (error.status == 0)
                {
                    FNC.loading('hide');
                    alert(_t('网络链接异常'));
                }
                else if (error.status == 500)
                {
                    FNC.loading('hide');
                    alert(_t('内部服务器错误'));
                }
            }
        });
    },
    
    // ajax提交callback
    ajax_processer: function (type, result)
    {
        FNC.loading('hide');
        
        if (typeof (result.errno) == 'undefined')
        {
            FNC.alert(result);
        }
        else if (result.errno != 1)//出错
        {
            switch (type)
            {
                case 'default':
                    FNC.alert(result.err);
                case 'error_message':
                    if (!$('.error_message').length)
                    {
                        alert(result.err);
                    }
                    else if ($('.error_message em').length)
                    {
                        $('.error_message em').html(result.err);
                    }
                    else
                    {
                         $('.error_message').html(result.err);
                    }
                    
                    if ($('.error_message').css('display') != 'none')
                    {
                        FNC.shake($('.error_message'));
                    }
                    else
                    {
                        $('.error_message').fadeIn();
                    }
                    
                    if ($('#captcha').length)
                    {
                        $('#captcha').click();
                    }
                break;
            }
        }
        else if (result.rsm && result.rsm.url)
        {
            // 判断返回url跟当前url是否相同
            if (window.location.href == result.rsm.url)
            {
                window.location.reload();
            }
            else
            {
                window.location = decodeURIComponent(result.rsm.url);
            }
        }
        else
        {
            window.location.reload();
        }
    },
    
    // 警告弹窗
    alert: function (text)
    {
        if ($('.alert-box').length)
        {
            $('.alert-box').remove();
        }
        
        $('.fm-ajax-box').append(Hogan.compile(FNC_TEMPLATE.alertBox).render(
        {
            message: text
        }));
        
        $(".alert-box").modal('show');
    },
    
    // 错误提示效果
    shake: function(selector)
    {
        var length = 6;
        selector.css('position', 'relative');
        for (var i = 1; i <= length; i++)
        {
            if (i % 2 == 0)
            {
                if (i == length)
                {
                    selector.animate({ 'left': 0 }, 50);
                }
                else
                {
                    selector.animate({ 'left': 10 }, 50);
                }
            }
            else
            {
                selector.animate({ 'left': -10 }, 50);
            }
        }
    },
}

// 全局变量
FNC.G =
{
    loading_timer: '',
    loading_bg_count: 12,
    loading_mini_bg_count: 9,
}

FNC.User =
{
    // 提交评论
    save_comment: function(selector)
    {
        selector.addClass('disabled');
        FNC.ajax_post(selector.parents('form'), FNC.ajax_processer, 'comments_form');
    },
    
    // 分享
    share_out: function(options)
    {
        var url = options.url || window.location.href, pic = '';
        
        if (options.title)
        {
            var title = options.title + ' - ' + G_SITE_NAME;
        }
        else
        {
            var title = $('title').text();
        }
        
        shareURL = 'http://www.jiathis.com/send/?webid=' + options.webid + '&url=' + url + '&title=' + title +'';
        
        if (options.content)
        {
            if ($(options.content).find('img').length)
            {
                shareURL = shareURL + '&pic=' + $(options.content).find('img').eq(0).attr('src');
            }
        }
        
        window.open(shareURL);
    },
}

function _t(string, replace)
{
    if (typeof (FNC_lang) != 'undefined')
    {
        if (typeof (FNC_lang[string]) != 'undefined')
        {
            string = FNC_lang[string];
        }
    }
    
    if (replace)
    {
        string = string.replace('%s', replace);
    }

    return string;
};
