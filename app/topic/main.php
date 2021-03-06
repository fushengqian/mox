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
     * 话题详情
     * */
    public function index_action()
    {
        TPL::import_css('css/base.css');

        $topic_id = trim($_GET['id']);

        $topic = $this->model('topic') -> fetch_row('topic', 'id = "'.$topic_id.'"');
        TPL::assign('topic', $topic);

        $feed_list = $this->model('feed') -> get_data_list(array('topic_id = "'.$topic_id.'"'));
        TPL::assign('feed_list', $feed_list);

        TPL::assign('topic_id', $topic_id);

        // Mox推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        $seo = array('title' => array(strip_tags($topic['title'])),
                     'description' => array($topic['words']));
        TPL::assign('seo', get_seo('topic', $seo));

        TPL::output('topic/detail');
    }
}