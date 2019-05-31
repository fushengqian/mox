<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class main extends MOX_CONTROLLER
{
    /**
     * 提交评论
     **/
    public function comment_action()
    {
        $user_id = intval(MOX_APP::session()->info['uid']);

        if (empty($user_id)) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先登录哦~'));
        }

        $target_id = trim($_POST['target_id']);
        $parent_id = !empty($_POST['parent_id']) ? trim($_POST['parent_id']) : 0;
        $content   = trim($_POST['content']);

        if (empty($target_id) || empty($content)) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '系统出错，请稍后再试~'));
        }

        $user_id = $this->model('user')->get_us($user_id);

        $result = $this->model('comment')->comment($target_id, $parent_id, 'feed', $user_id, $content);
        if (!$result) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '评论成功失败！'));
        }

        $user_info = $this->model('user')->get_user_info_by_id($user_id);
        $user_info['avatar'] = G_DEMAIN.$user_info['avatar'];
        $arr = array('user_info' => $user_info, 'comment' => $content);

        //发送消息
        $feed = $this->model('like')->fetch_row('feed', "id = '".$target_id."'");
        $url = G_DEMAIN.'/feed/'.$target_id.'.html';
        $this->model('message')->send($feed['user_id'], 0, '圈友 <b>'.$user_info['user_name'].'</b> 评论了您的动态 <span style="color:#2d64b3;">“'.summary(strip_tags($content), 30).'”</span>，快去看看吧！', $url, 'comment', $target_id);

        $this->model('points')->send($user_id, 'comment');

        H::ajax_json_output(MOX_APP::RSM($arr, 1, '评论成功！'));
    }
}
