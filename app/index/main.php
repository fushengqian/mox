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
     * 网站首页
     * */
    public function index_action()
    {
        // 热门话题
        $hot_topic_list = $this->model('topic')-> get_data_list(array('is_home = 1'), 1, 3);
        TPL::assign('hot_topic_list', $hot_topic_list);

        $feed_list = $this->model('feed')-> get_data_list(array('is_home = 1'));
        TPL::assign('feed_list', $feed_list);

        // Mox推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        // 积分动向
        $point_list = $this->model('points')-> get_data_list(array('point > 50'), 1, 5, 'create_time desc');
        TPL::assign('point_list', $point_list);

        // 新注册人
        $user_list = $this->model('user')-> get_data_list(array('is_us = 0'), 1, 10, 'reg_time desc');
        TPL::assign('new_user_list', $user_list);

        // banner 图
        $banner_list = $this->model('ad')-> get_banner(6);
        TPL::assign('banner_list', $banner_list);

        // activity 图
        $activity_list = $this->model('ad')-> get_activity(1);
        TPL::assign('activity_list', $activity_list);

        // 商城分类
        $mall_nav = MOX_APP::$settings['mall_nav'];
        TPL::assign('mall_nav', $mall_nav);

        TPL::assign('seo', get_seo('index'));

        TPL::output('index/index');
    }
}
