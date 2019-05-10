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

class feed extends MOX_CONTROLLER
{
    /**
     * 我的feed列表
     * */
    public function index_action()
    {
        TPL::import_css('css/user.css');
        
        TPL::assign('seo', get_seo('user_feed'));
        
        $user_id = MOX_APP::session()->info['uid'];
        
        if (empty($user_id))
        {
            HTTP::redirect(G_DEMAIN.'/user/login/');
            exit;
        }

        $user_info = $this->model('user') -> get_user_info_by_id($user_id);
        $list = $this->model('feed') -> get_data_list('mox_id = '.intval($user_info['mox_id']), 1, 100, 'id desc');
        
        TPL::assign('list', $list);
        
        TPL::output('user/feed/index');
    }
}