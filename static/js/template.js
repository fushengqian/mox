var FNC_TEMPLATE = {
    'loadingBox':
        '<div id="fm-loading" class="hide">'+
            '<div id="fm-loading-box"></div>'+
        '</div>',

    'loadingMiniBox':
        '<div id="fm-loading-mini-box"></div>',
        
    'alertBox' :
            '<div class="modal fade alert-box aw-tips-box">'+
                '<div class="modal-dialog">'+
                    '<div class="modal-content">'+
                        '<div class="modal-header">'+
                            '<a type="button" class="close icon icon-delete" data-dismiss="modal" aria-hidden="true"></a>'+
                            '<h3 class="modal-title" id="myModalLabel">提示信息</h3>'+
                        '</div>'+
                        '<div class="modal-body">'+
                            '<p>{{message}}</p>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>',
    'ajaxData' :
        '<div class="modal fade alert-box aw-topic-edit-note-box aw-question-edit" aria-labelledby="myModalLabel" role="dialog">'+
            '<div class="modal-dialog">'+
                '<div class="modal-content">'+
                    '<div class="modal-header">'+
                        '<a type="button" class="close icon icon-delete" data-dismiss="modal" aria-hidden="true"></a>'+
                        '<h3 class="modal-title" id="myModalLabel">{{title}}</h3>'+
                    '</div>'+
                    '<div class="modal-body">'+
                        '{{data}}'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>',

    'commentBox' :
            '<div class="aw-comment-box" id="{{comment_form_id}}">'+
                '<div class="aw-comment-list"><p align="center" class="aw-padding10"><i class="aw-loading"></i></p></div>'+
                '<form action="{{comment_form_action}}" method="post" onsubmit="return false">'+
                    '<div class="aw-comment-box-main">'+
                        '<textarea class="aw-comment-txt form-control" rows="2" name="message" placeholder="评论一下..."></textarea>'+
                        '<div class="aw-comment-box-btn">'+
                            '<span class="pull-right">'+
                                '<a href="javascript:;" class="btn btn-mini btn-success" onclick="AWS.User.save_comment($(this));">评论</a>'+
                                '<a href="javascript:;" class="btn btn-mini btn-gray close-comment-box">取消</a>'+
                            '</span>'+
                        '</div>'+
                    '</div>'+
                '</form>'+
            '</div>',

    'commentBoxClose' :
            '<div class="aw-comment-box" id="{{comment_form_id}}">'+
                '<div class="aw-comment-list"><p align="center" class="aw-padding10"><i class="aw-loading"></i></p></div>'+
            '</div>',

    'dropdownList' :
        '<div aria-labelledby="dropdownMenu" role="menu" class="aw-dropdown">'+
            '<ul class="aw-dropdown-list">'+
            '{{#items}}'+
                '<li><a data-value="{{id}}">{{title}}</a></li>'+
            '{{/items}}'+
            '</ul>'+
        '</div>',

    'alertImg' :
        '<div class="modal fade alert-box aw-tips-box aw-alert-img-box">'+
            '<div class="modal-dialog">'+
                '<div class="modal-content">'+
                    '<div class="modal-header">'+
                        '<a type="button" class="close icon icon-delete" data-dismiss="modal" aria-hidden="true"></a>'+
                        '<h3 class="modal-title" id="myModalLabel">提示信息</h3>'+
                    '</div>'+
                    '<div class="modal-body">'+
                        '<p class="hide {{hide}}">{{message}}</p>'+
                        '<img src="{{url}}" />'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>',

    'confirmBox' :
        '<div class="modal fade alert-box aw-confirm-box">'+
            '<div class="modal-dialog">'+
                '<div class="modal-content">'+
                    '<div class="modal-header">'+
                        '<a type="button" class="close icon icon-delete" data-dismiss="modal" aria-hidden="true"></a>'+
                        '<h3 class="modal-title" id="myModalLabel">提示信息</h3>'+
                    '</div>'+
                    '<div class="modal-body">'+
                        '{{message}}'+
                    '</div>'+
                    '<div class="modal-footer">'+
                        '<a class="btn btn-gray" data-dismiss="modal" aria-hidden="true">取消</a>'+
                        '<a class="btn btn-success yes">确定</a>'+
                    '</div>'+
                '</div>'+
            '</div>'+
        '</div>',
}
