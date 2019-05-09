<?php

class main extends MOX_CONTROLLER
{
    /**
     * 动态主页
     * */
    public function index_action()
    {
        TPL::import_css('css/base.css');

        TPL::assign('seo', get_seo('usercenter'));

        $user_id = MOX_APP::session()->info['uid'];

        $honor = $this->model('points')->getHonor($this->user_info['point']);
        TPL::assign('honor', $honor);

        $feed_list = $this->model('feed')-> get_data_list(array('user_id' => $user_id));
        TPL::assign('feed_list', $feed_list);

        // 模型圈推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        $this->model('points')->send($user_id, 'visit_home');

        TPL::assign('seo', get_seo('home'));

        TPL::output('home/index');
    }
}