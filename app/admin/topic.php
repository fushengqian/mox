<?php
class topic extends FARM_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $this->crumb(FARM_APP::lang()->_t('话题管理'), "admin/topic/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(804));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`content` like "%' . $keyword . '%" )';
        }

        $list = $this->model('topic')->get_data_list($where, $_GET['aid'], 20);

        TPL::assign('list', $list);

        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/topic/index/'),
            'total_rows' => $this->model('topic')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('topic')->found_rows());

        TPL::output('admin/topic/list');
    }

    public function delete_action()
    {
        $id = $_GET['id'];

        $this->model('topic')->delete('feed', 'id = ' . trim($id));

        HTTP::redirect('/admin/topic/index/');
        exit;
    }

    public function recommend_action()
    {
        $id = $_GET['id'];

        $this->model('topic')->update('topic', array('is_home' => 1), 'id = ' . trim($id));

        HTTP::redirect('/admin/topic/index/');
        exit;
    }
}