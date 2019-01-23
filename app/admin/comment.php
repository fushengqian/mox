<?php
class comment extends FARM_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }
    
    public function list_action()
    {
        $this->crumb(FARM_APP::lang()->_t('点评列表'), "admin/comment/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(300));
        
        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';
        $is_default = $_GET['is_default'] ? intval($_GET['is_default']) : '';
        
        $where = '1 = 1';
        if ($keyword)
        {
            $where .= ' AND `content` like "%'.$keyword.'%"';
        }
        
        if ($is_default)
        {
            $where .= ' AND `is_default` = 1';
        }
        
        $list = $this->model('comment')->get_data_list($where, $_GET['aid'], 20, 'id desc');
        
        foreach($list as $k => $v)
        {
            if ($v['type'] == '1')
            {
                $list[$k]['farm'] = $this->model('farm')->get_one(encode($v['target_id']));
            }
        }
        
        TPL::assign('list', $list);
        TPL::assign('is_default', $is_default);
        
        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/comment/list/'),
            'total_rows' => $this->model('comment')->found_rows(),
            'per_page' => 20
        ))->create_links());
        
        TPL::assign('total_rows', $this->model('comment')->found_rows());
        
        
        TPL::output('admin/comment/list');
    }
    
    //删除点评
    public function delete_action()
    {
        $id = intval($_GET['id']);
        
        $result = $this -> model('comment') -> delete('comment', 'id = '.intval($id));
        
        HTTP::redirect('/admin/comment/list/');
    }
    
    //删除点评
    public function update_action()
    {
        $id = intval($_GET['id']);
        
        $result = $this -> model('comment') -> update('comment', array('is_default' => 1), 'id = '.$id);;
        
        HTTP::redirect('/admin/comment/list/');
    }
}
