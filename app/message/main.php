<?php
class main extends MOX_CONTROLLER
{
    /**
     * 消息列表
     * */
    public function index_action()
    {
        $user_id = MOX_APP::session()->info['uid'];

        if (empty($user_id)) {
            HTTP::redirect(G_DEMAIN . '/user/login/');
            exit;
        }

        $honor = $this->model('points')->getHonor($this->user_info['point']);
        TPL::assign('honor', $honor);

        $msg_list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id)), 1, 1000);
        TPL::assign('msg_list', $msg_list);

        // 全部消息置为已读
        $this->model('message')->set_readed($user_id);

        TPL::import_css('css/base.css');
        TPL::output('message/index');
    }
}
