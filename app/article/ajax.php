<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class ajax extends MOX_CONTROLLER
{
    public function publish_action()
    {
        $user_info = MOX_APP::user()->get_info();

        if (empty($user_info['uid']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, - 1, '请先登录哦~'));
        }

        if (!$_POST['title'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, - 1, '请输入文章标题！'));
        }

        if (!$_POST['content'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, - 1, '请输入文章内容！'));
        }

        if ($_POST['is_preview']) {
            $_POST['status'] = 2;
        } else {
            $_POST['status'] = 1;
        }
        
        $id       = intval($_POST['id']);
        $title    = $_POST['title'];
        $content  = $_POST['content'];
        $keywords = $_POST['keywords'];
        $summary  = $_POST['summary'];
        $from     = $_POST['from'] ? $_POST['from'] : 'Mox';
        $status   = intval($_POST['status']);
        $user_id  = intval(MOX_APP::session()->info['uid']);
        $cate  = $_POST['cate'];
        
        $artcle_id = $this -> model('article') -> publish($id, $cate, $title, $content, $keywords, $from, $summary, $status, $user_id);

        H::ajax_json_output(MOX_APP::RSM(array(
                'url' => G_DEMAIN.'/article/'.$artcle_id.'.html'
        ), 1, '发布文章成功！'));
    }
}
