<?php
/*
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
+---------------------------------------------------------------------------
*/

if (!defined('IN_MOX'))
{
    die;
}

class category extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        $this->crumb(MOX_APP::lang()->_t('分类管理'), "admin/category/list/");
        
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::redirect_msg(MOX_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(701));
    }
    
    public function list_action()
    {
        TPL::assign('list', json_decode($this->model('system')->build_category_json(), true));
        
        TPL::assign('category_option', $this->model('system')->build_category_html(0, 0, null, false));
        
        TPL::assign('target_category', $this->model('system')->build_category_html(0, null));
        
        TPL::output('admin/category/list');
    }
    
    public function edit_action()
    {
        if (!$category_info = $this->model('system')->get_category_info($_GET['category_id']))
        {
            H::redirect_msg(MOX_APP::lang()->_t('指定分类不存在'), '/admin/category/list/');
        }
        
        TPL::assign('category', $category_info);
        TPL::assign('category_option', $this->model('system')->build_category_html(0, $category['parent_id'], null, false));
        
        TPL::output('admin/category/edit');
    }
}