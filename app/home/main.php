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
     * 动态主页
     * */
    public function index_action()
    {
        $tab = !empty($_GET['id']) ? trim($_GET['id']) : 'focus';

        TPL::import_css('css/base.css');

        TPL::assign('seo', get_seo('usercenter'));

        $my_user_id = MOX_APP::session()->info['uid'];
        if (!$my_user_id) {
            $my_user_id = 0;
        }

        // 我的关注
        if ($tab == 'focus') {
            $my_follower_list =  $this -> model('system')->fetch_all('follow', "user_id = '".$my_user_id."'");
            $my_follower = array($my_user_id);
            if ($my_follower_list) {
                foreach ($my_follower_list as $f){
                    $my_follower[] = $f['follow_user_id'];
                }
            }
            $where[] = 'user_id in ('.implode(',', array_unique($my_follower)).')';
        }

        // 我的
        if ($tab == 'my') {
            $where[] = 'user_id = "'.$my_user_id.'"';
        }

        // 热门动态
        if ($tab == 'hot') {
            $where[] = "is_home = 1";
        }

        $honor = $this->model('points')->getHonor($this->user_info['point']);
        TPL::assign('honor', $honor);

        $feed_list = $this->model('feed')-> get_data_list($where);
        TPL::assign('feed_list', $feed_list);

        // Mox推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        $this->model('points')->send(0, 'visit_home');

        TPL::assign('seo', get_seo('home'));
        TPL::assign('tab', $tab);

        TPL::output('home/index');
    }
}