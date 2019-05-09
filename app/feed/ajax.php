<?php

class ajax extends MOX_CONTROLLER
{
    /**
     * 发布动态
     **/
    public function create_action()
    {
        $user_info = MOX_APP::user()->get_info();

        if (empty($user_info['uid'])) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先登录哦~'));
        }

        if (!$_POST['content']) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先输入内容哦~'));
        }

        $content  = trim($_POST['content']);
        $pics     = trim($_POST['pic']);
        $topic_id = trim($_POST['topic_id']);

        if (!empty($pics)) {
            $pics = explode(',', $pics);
        }

        $user_id = intval(MOX_APP::session()->info['uid']);
        $user_id = $this->model('user')->get_us($user_id);

        $url = $this->model('feed')->create($content, $pics, $user_id, $topic_id);

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => $url
        ), 1, '发布成功！'));
    }
}
