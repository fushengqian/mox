<?php
class sms extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }

    //短信列表
    public function list_action()
    {
        $mobile = $_GET['mobile'] ? trim($_GET['mobile']) : '';

        $this->crumb(MOX_APP::lang()->_t('已发短信'), "admin/sms/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(801));
        
        $where = '1 = 1';

        if ($mobile)
        {
            $where .= ' AND `mobile` = "'.$mobile.'"';
        }

        $list = $this->model('sms')->get_data_list($where, $_GET['aid'], 20, 'id desc');
        
        TPL::assign('list', $list);
        
        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/sms/list/'),
            'total_rows' => $this->model('sms')->found_rows(),
            'per_page' => 20
        ))->create_links());
        
        TPL::assign('total_rows', $this->model('sms')->found_rows());
        
        TPL::output('admin/sms/list');
    }
}
