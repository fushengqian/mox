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

class main extends MOX_CONTROLLER
{
    /**
     * 点赞
     **/
    public function like_action()
    {
        $user_id = intval(MOX_APP::session()->info['uid']);

        if (empty($user_id)) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先登录哦~'));
        }

        $target_id = trim($_POST['target_id']);
        if (empty($target_id)) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '系统出错了~'));
        }

        $user_id = $this->model('user')->get_us($user_id);

        $result = $this->model('like')->dolike($target_id, 'feed', $user_id);
        if (!$result) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '点赞失败！'));
        }

        //发送消息
        $feed = $this->model('like')->fetch_row('feed', "id = '".$target_id."'");
        $user_info = $this->model('user')-> get_user_info_by_id($user_id);
        $url = G_DEMAIN.'/feed/'.$target_id.'.html';
        $this->model('message')->send($feed['user_id'], $user_id, '圈友 <b>'.$user_info['user_name'].'</b> 赞了您的动态 <span style="color:#2d64b3;">“'.summary(strip_tags($feed['content']), 30).'”</span>，快去看看吧！', $url, 'like', $target_id);

        $this->model('points')->send($user_id, 'like');

        H::ajax_json_output(MOX_APP::RSM(array(
        ), 1, '操作成功！'));
    }
}
