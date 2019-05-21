<?php
class page extends MOX_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('页面管理'), "backend/hot/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(803));
        
        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';
        
        $where = '1 = 1';
        if ($keyword)
        {
            $where .= ' AND (`title` like "%'.$keyword.'%" OR `keywords` like "'.$keyword.'")';
        }
        
        $list = $this->model('page')->get_data_list($where, $_GET['aid'], 20);
        
        TPL::assign('list', $list);
        
        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/page/index/'),
            'total_rows' => $this->model('page')->found_rows(),
            'per_page' => 20
        ))->create_links());
        
        TPL::assign('total_rows', $this->model('page')->found_rows());
        
        TPL::output('backend/page/list');
    }
    
    public function delete_action()
    {
        $id = $_GET['id'];
        
        $result = $this -> model('mox') -> delete('page', 'id = '.intval($id));
        
        HTTP::redirect('/backend/page/index/');
        
        exit;
    }
    
    public function recommend_action()
    {
        $position = $_REQUEST['position'];
        $title    = trim($_REQUEST['title']);
        $url      = trim($_REQUEST['url']);
        $image    = trim($_REQUEST['image']);
        $brief    = trim($_REQUEST['brief']);
        
        $info = array('position' => $position,
                      'title'    => $title,
                      'url'      => $url,
                      'image'    => $image,
                      'brief'    => $brief,
                      'create_time' => time(),
                      'status'   => 1);
        
        $data = $this -> model('mox') -> fetch_row('recommend', "`position` = '".$position."' AND url = '".$url."'");
        
        if (!$data)
        {
            $result = $this -> model('page') -> insert('recommend', $info);
        }
        
        if ($result)
        {
            H::ajax_json_output(MOX_APP::RSM(array(), 1, '后台推送页面成功'));
        }
        else
        {
            H::ajax_json_output(MOX_APP::RSM(array(), -1, '后台推送页面失败'));
        }
    }
}