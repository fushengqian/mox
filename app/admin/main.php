<?php
class main extends FARM_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $this->crumb(FARM_APP::lang()->_t('概述'), 'admin/main/');
        
        TPL::assign('users_count', $this->model('system')->count('user'));

        TPL::assign('feed_count', $this->model('system')->count('feed'));

        TPL::assign('topic_count', $this->model('system')->count('topic'));

        TPL::assign('like_count', $this->model('system')->count('like'));

        TPL::assign('comment_count', $this->model('system')->count('comment'));
        
        $admin_menu = (array)FARM_APP::config()->get('admin_menu');
        
        $admin_menu[0]['select'] = true;
        
        TPL::assign('menu_list', $admin_menu);
        
        TPL::output('admin/index');
    }
    
    public function login_action()
    {
        if (FARM_APP::session()->admin_login)
        {
            HTTP::redirect('/admin/');
        }
        
        TPL::import_css('admin/css/login.css');
        
        TPL::output('admin/login');
    }
    
    public function logout_action($return_url = '/')
    {
        $this->model('admin')->admin_logout();
        
        HTTP::redirect($return_url);
    }
    
    public function settings_action()
    {
        $this->crumb(FARM_APP::lang()->_t('系统设置'), 'admin/settings/');
        
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::redirect_msg(FARM_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }
        
        if (!$_GET['category'])
        {
            $_GET['category'] = 'site';
        }
        
        switch ($_GET['category'])
        {
            case 'interface':
                TPL::assign('styles', $this->model('setting')->get_ui_styles());
            break;
        }
        
        TPL::assign('setting', get_setting(null, false));
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list('SETTINGS_' . strtoupper($_GET['category'])));
        
        TPL::output('admin/settings');
    }
    
    public function nav_menu_action()
    {
        $this->crumb(FARM_APP::lang()->_t('导航设置'), 'admin/nav_menu/');
        
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::redirect_msg(FARM_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }
        
        TPL::assign('nav_menu_list', $this->model('menu')->get_nav_menu_list());
        
        TPL::assign('setting', get_setting());
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(700));
        
        TPL::output('admin/nav_menu');
    }
}