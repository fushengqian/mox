<?php
/*
+--------------------------------------------------------------------------
|   FarmNc 
|   ========================================
|   by FarmNc Software
|   © 2015 - 2016 FarmNc. All Rights Reserved
|   http://www.farmNc.net
|   ========================================
|   Support: 540335306@qq.com
+---------------------------------------------------------------------------
*/

if (!defined('IN_FARMNC'))
{
    die;
}

class help extends FARM_ADMIN_CONTROLLER
{
    public function setup()
    {
        $this->crumb(FARM_APP::lang()->_t('帮助中心'), "admin/help/list/");

        if (!$this->user_info['permission']['is_administortar'])
        {
            H::redirect_msg(FARM_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(305));
    }

    public function list_action()
    {
        TPL::assign('chapter_list', $this->model('help')->get_chapter_list());

        TPL::output('admin/help/list');
    }

    public function edit_action()
    {
        if ($_GET['id'])
        {
            $chapter_info = $this->model('help')->get_chapter_by_id($_GET['id']);

            if (!$chapter_info)
            {
                H::redirect_msg(FARM_APP::lang()->_t('指定章节不存在'), '/admin/help/list/');
            }
            
            TPL::assign('chapter_info', $chapter_info);
            
            $data_list = $this->model('help')->get_data_list($chapter_info['id']);
            
            if ($data_list)
            {
                TPL::assign('data_list', $data_list);
            }
        }

        TPL::output('admin/help/edit');
    }
}
