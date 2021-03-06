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
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请输入内容！'));
        }

        $content = trim($_POST['content']);
        $pics    = trim($_POST['pic']);

        if (!empty($pics)) {
            $pics = explode(',', $pics);
        }

        $user_id = intval(MOX_APP::session()->info['uid']);

        $url = $this->model('feed')->create($content, $pics, $user_id);

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => $url
        ), 1, '发布成功！'));
    }
}
