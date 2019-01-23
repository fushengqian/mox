<?php
class city extends FARM_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $page = $_GET['aid'] ? $_GET['aid'] : 1;
        $order = $_GET['order'] ? $_GET['order'] : 'provid asc';
        
        $this->crumb(FARM_APP::lang()->_t('城市管理'), "admin/hot/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(400));
        
        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';
        $where = 'id > 1';
        
        if ($keyword)
        {
            $p = $this -> model('system') -> get_province_by_name($keyword);
        }
        
        if ($p)
        {
            $pid = $p['piovid'];
        }
        else if($keyword)
        {
            $where .= ' AND (`name` like "%'.$keyword.'%")';
        }
        
        if ($pid)
        {
            $where .= ' AND `provid` = '.intval($pid);
        }
        
        $list = $this -> model('system') -> fetch_page('city', $where, $order, $page, 50);
        
        $province_list = $this -> model('system') -> get_all_province();
        
        empty($list) && $list = array();
        foreach($list as $k => $v)
        {
            foreach($province_list as $k1 => $v1)
            {
                if ($v1['piovid'] == $v['provid'])
                {
                    $list[$k]['pname'] = $v1['piovname'];
                }
            }
        }
        
        TPL::assign('list', $list);
        
        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/city/index/'),
            'total_rows' => $this->model('system')->found_rows(),
            'per_page' => 50
        ))->create_links());
        
        TPL::assign('total_rows', $this->model('system')->found_rows());
        TPL::assign('order', $order);
        
        TPL::output('admin/city/list');
    }
    
    public function update_action()
    {
        $uname = trim($_POST['uname']);
        $id  = intval($_POST['id']);
        $status = $_POST['status'];
        
        $result = $this -> model('farm') -> update('city', array('uname' => $uname, 'status' => $status), 'id = '.$id);
        
        if ($result)
        {
            H::ajax_json_output(FARM_APP::RSM(array(), 1, null));
        }
        else
        {
            H::ajax_json_output('保存失败');
        }
    }
}