/*!
 * FileUpload v1.0
 * Copyright 2015-2016 Mox, Inc.
 * Date: 2015-11-24
 */
function FileUpload (type, element, container, url, options, callback)
{
    var _this = this;
    this.type = type;
    this.element = element;
    this.container = container;
    this.url = url;
    this.options = {
        'multiple' : true,
        'deleteBtn' : true,
        'insertBtn' : true,
        'one' : false,
        'insertTextarea' : '.wmd-input',
        'template' : '<li>'+
                        '<div class="img"></div>'+
                        '<div class="upload-file-desc">'+
                            '<p class="meta"></p>'+
                        '</div>'+
                        '</li>',
        'deleteBtnTemplate' : '<a class="delete-file">删除</a>' ,
        'insertBtnTemplate' : '<a class="insert-file">上移↑</a>'
    };

    this.options = $.extend(this.options, options);
    
    this.callback = callback;

    if (type == 'file')
    {
        this.init(element, container);
    }
    else
    {
        var form = this.createForm(),
            input = this.createInput();
        $(element).prepend($(form).append(input));
    }
}

FileUpload.prototype = 
{
    // 初始化上传器
    init : function (element, container)
    {
        var form = this.createForm(),
            input = this.createInput();
        
        $(element).prepend($(form).append(input));
    },

    // 创建表单
    createForm : function ()
    {
        var form = this.toElement('<form method="post" enctype="multipart/form-data"><input type="submit" class="submit" /></form>');
        $(form).attr({
            'id' : 'upload-form',
            'action' : this.url,
            'target' : 'ajaxUpload'
        });

        this.form = form;

        return form;
    },

    // 创建input
    createInput : function ()
    {
        var _this = this, input = this.toElement('<input type="file" />');

        $(input).attr({
            'class' : 'file-input',
            'name' : 'aws_upload_file',
            'multiple' : this.options.multiple ? 'multiple' : false
        });
        
        $(input).change(function()
        {
            _this.addFileList(this);
        });

        return input;
    },

    // 创建隐藏域 
    createHiddenInput : function(file_name)
    {
        var _this = this, input = this.toElement('<input type="hidden" name="files[]" class="hidden-input" />');

        $(input).val(file_name);

        return input;
    },

    // 创建iframe
    createIframe : function ()
    {
        var iframe = this.toElement('<iframe></iframe>');
        $(iframe).attr({
            'class': 'hide upload-iframe',
            'name': 'ajaxUpload'
        });
        return iframe;
    },

    // 添加文件列表
    addFileList : function (input)
    {
        var files = $(input)[0].files;
        if (files && this.type == 'file')
        {
            for (i = 0; i < files.length; i++)
            {
                this.li = this.toElement(this.options.template);
                this.file = files[i];
                if (this.options.one)
                {
                    $(".upload-content").find('.upload-list').html('');
                }
                $(".upload-content").find('.upload-list').append(this.li);
                this.upload(files[i], this.li);
            }
        }
        else
        {
            if (this.type == 'file')
            {
                this.li = this.toElement(this.options.template);
                $(".upload-content").find('.upload-list').append(this.li);
                this.upload('', this.li);
            }
            else
            {
                this.upload('');
            }
        }
        
    },

    // 上传功能
    upload : function (file, li)
    {
        var _this = this;

        if (file)
        {
            var xhr = new XMLHttpRequest(), status = false;

            xhr.upload.onprogress = function(event)
            {
                if (event.lengthComputable)
                {
                    var percent = Math.round(event.loaded * 100 / event.total);
                }

                $(li).find('.file-title').html(file.name);

                $(li).find('.file-size').html(percent + '%');
            };

            xhr.onreadystatechange = function()
            {      
                _this.oncomplete(xhr, li, file);
            };

            var url = this.url + '&aws_upload_file=' + file.name + '&timestamp=' + new Date().getTime();

            xhr.open("POST", url);

            xhr.send(file);
        }
        else
        {
            //低版本ie上传
            var iframe = this.createIframe();

            if (this.options.loading_status)
            {
                $(this.options.loading_status).show();
            }

            if (iframe.addEventListener)
            {
                iframe.addEventListener('load', function()
                {
                    _this.getIframeContentJSON(iframe, _this.container);
                }, false);
            } else if (iframe.attachEvent)
            {
                iframe.attachEvent('onload', function()
                {
                    _this.getIframeContentJSON(iframe, _this.container);
                });
            }

            $('#aw-ajax-box').append(iframe);

            $(this.form).find('.submit').click();
        }
    },

    // 从iframe获取json内容
    getIframeContentJSON : function (iframe, container)
    {
        var doc = iframe.contentDocument ? iframe.contentDocument: iframe.contentWindow.document,
            response, filename;
            
            response = eval("(" + doc.body.innerHTML + ")");
            
            if (this.type == 'file')
            {
                this.render(this.li, response);
            }
            else
            {
                $(this.options.loading_status).hide();

                if ($(this.container).attr('src'))
                {
                    $(this.container).attr('src', response.thumb + '?' + Math.round(Math.random() * 10000));
                }
                else
                {
                    $(this.container).css(
                    {
                        'background' : 'url(' + response.thumb + '?' + Math.round(Math.random() * 10000) + ')'
                    });
                }
            }

               $('.upload-iframe').detach();

               this.oncallback();
    },

    // ajax完成callback
    oncomplete : function (xhr, li, file)
    {
        var _this = this, response, filesize = this.getFIleSize(file);
        if (xhr.readyState == 4)
        {
            if (xhr.status == 200)
            {
                try
                {
                    window.location.reload();
                }
                catch(err)
                {
                    response = {};
                }
            }
            else if (xhr.status == 500)
            {
                this.render(li, {'error':_t('内部服务器错误')}, filesize);
            }
            else if (xhr.status == 0)
            {
                this.render(li, {'error':_t('网络链接异常')}, filesize);
            }
        }
    },

    // 此功能配合编辑器
    oncallback : function ()
    {
        if (this.callback)
           {
               this.callback();
           }
    },

    // 渲染缩略列表
    render : function (element, json, filesize)
    {
        if (json)
        {
            if (!json.error)
            {
                switch (json.class_name)
                {
                    case 'txt':
                        $(element).find('.img').addClass('file').html('<i class="icon icon-file"></i>');
                    break;

                    default :
                        $(element).find('.img').css(
                        {
                            'background': 'url("' + json.thumb + '")'
                        }).addClass('active').attr('data-img', json.thumb);
                    break;
                }

                if (filesize)
                {
                    $(element).find('.size').html(filesize);
                }

                if (this.options.deleteBtn && json.delete_url)
                {
                    var btn = this.createDeleteBtn(json.delete_url);

                    $(element).find('.meta').append(btn);
                }
                
                if (this.options.insertBtn && json.delete_url && !json.class_name)
                {
                    var btn = this.createInsertBtn(json.file_name);
                    $(element).find('.meta').append(btn);
                }

                // 插入隐藏域(wecenter定制)
                $(element).append(this.createHiddenInput(json.file_name));

                this.oncallback();
            }
            else
            {
                $(element).addClass('error').find('.img').addClass('error').html('<i class="icon icon-delete"></i>');
                
                $(element).find('.size').text(json.error);
            }
        }
    },

    toElement : function (html)
    {
        var div = document.createElement('div');
        div.innerHTML = html;
        var element = div.firstChild;
        div.removeChild(element);
        return element;
    },

    // 获取文件名
    getName : function (filename)
    {
        return filename.replace(/.*(\/|\\)/, "");
    },

    // 获取文件大小
    getFIleSize : function (file)
    {
        var filesize;
        if (file.size > 1024 * 1024)
        {
            filesize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        }
        else
        {
            filesize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
        }
        return filesize;
    },

    // 创建插入按钮
    createInsertBtn : function (file_name)
    {
        var btn = this.toElement(this.options.insertBtnTemplate), _this = this;

        $(btn).click(function()
        {
            var $li = $(this).parents("li");
            if ($li.index() != 0)
            {
                $li.prev().before($li);
            }
        });

        return btn;
    },

    // 创建删除按钮
       createDeleteBtn : function (url)
       {
           var btn = this.toElement(this.options.deleteBtnTemplate);
           
           $(btn).click(function()
        {
            var _this = this;
               AWS.dialog('confirm',{'message': '确定要删除吗？'}, function(){
                $.get(url, function (result)
                {
                    if (result.errno == "-1")
                    {
                        AWS.alert(result.err);
                    }
                    else
                    {
                        $(_this).parents('li').detach();
                    }
                }, 'json');
               });
               
        });

        return btn;
       }
}

