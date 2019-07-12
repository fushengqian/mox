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
     * 动态详情
     * */
    public function index_action()
    {
        $user_id = MOX_APP::session()->info['uid'];

        $feed_id = trim($_GET['id']);
        $feed = $this->model('feed')->get_detail($feed_id);
        TPL::assign('feed', $feed);

        // 是否关注
        $follow = $this -> model('system')->fetch_row('follow', "user_id = '".$user_id."' and follow_user_id = '".$feed['user_id']."'");
        TPL::assign('follow', $follow);

        // 是否点赞
        $like = $this -> model('system')->fetch_row('like', "user_id = '".$user_id."' and target_id = '".$feed['id']."'");
        TPL::assign('like', $like);

        // Mox推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        $this->model('points')->send($user_id, 'visit_feed_detail');

        $seo = array('title' => array(summary($feed['content'], 40)),
                     'keywords' => array('动态'),
                     'description' => array(summary(strip_tags($feed['content'], 100))));
        TPL::assign('seo', get_seo('feed-detail', $seo));

        TPL::output('feed/detail');
    }
}
