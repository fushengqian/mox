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

class tools extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::redirect_msg(MOX_APP::lang()->_t('你没有访问权限, 请重新登录'));
        }
        
        @set_time_limit(0);
    }
    
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('系统维护'), 'admin/tools/');
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(1000));
        
        TPL::output('admin/tools');
    }
    
    public function init_action()
    {
        H::redirect_msg(MOX_APP::lang()->_t('正在准备...'), '/admin/tools/' . $_POST['action'] . '/page-1__per_page-' . $_POST['per_page']);
    }
    
    public function cache_clean_action()
    {
        MOX_APP::cache()->clean();
        H::redirect_msg(MOX_APP::lang()->_t('缓存清理完成'), '/admin/tools/');
    }
    
    public function update_users_reputation_action()
    {
        if ($this->model('reputation')->calculate((($_GET['page'] * $_GET['per_page']) - $_GET['per_page']), $_GET['per_page']))
        {
            H::redirect_msg(MOX_APP::lang()->_t('正在更新用户经验') . ', ' . MOX_APP::lang()->_t('批次: %s', $_GET['page']), '/admin/tools/update_users_reputation/page-' . ($_GET['page'] + 1) . '__per_page-' . $_GET['per_page']);
        }
        else
        {
            H::redirect_msg(MOX_APP::lang()->_t('用户经验更新完成'), '/admin/tools/');
        }
    }

    public function email_setting_test_action()
    {
        if ($error_message = MOX_APP::mail()->send($_POST['test_email'], get_setting('site_name') . ' - ' . MOX_APP::lang()->_t('邮件服务器配置测试'), MOX_APP::lang()->_t('这是一封测试邮件，收到邮件表示邮件服务器配置成功'), get_setting('site_name')))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('测试邮件发送失败, 返回的信息: %s', strip_tags($error_message))));
        }
        else
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('测试邮件已发送, 请查收邮件测试配置是否正确')));
        }
    }

    public function update_weixin_menu_action()
    {
        $accounts_info = $this->model('weixin')->get_accounts_info();
        
        foreach ($accounts_info AS $account_info)
        {
            if ($error_message = $this->model('weixin')->update_client_menu($account_info))
            {
                $messages .= '<br />' . $error_message;
            }
        }
        
        if ($messages)
        {
            $messages = '更新微信菜单出现错误：<br />' . $messages;
        }
        else
        {
            $messages = '更新微信菜单完成';
        }
        
        H::redirect_msg(MOX_APP::lang()->_t($messages), '/admin/weixin/mp_menu/');
    }
}