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

class find_password extends MOX_CONTROLLER
{

    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'black'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
        $rule_action['actions'] = array();
        return $rule_action;
    }
    
    public function setup()
    {
        $this->crumb(MOX_APP::lang()->_t('找回密码'), '/account/find_password/');
        TPL::import_css('css/login-register.css');
    }
    
    public function index_action()
    {
        TPL::output('account/find_password/index');
    }
    
    public function process_success_action()
    {
        TPL::assign('email', MOX_APP::session()->find_password);
        TPL::output('account/find_password/process_success');
    }
    
    public function modify_action()
    {
        if (is_mobile())
        {
            HTTP::redirect('/m/find_password_modify/?key=' . $_GET['key']);
        }
        
        if (!$active_code_row = $this->model('active')->get_active_code($_GET['key'], 'FIND_PASSWORD'))
        {
            H::redirect_msg(MOX_APP::lang()->_t('链接已失效'), '/');
        }
        
        if ($active_code_row['active_time'] OR $active_code_row['active_ip'])
        {
            H::redirect_msg(MOX_APP::lang()->_t('链接已失效'), '/');
        }
        
        TPL::output('account/find_password/modify');
    }
}