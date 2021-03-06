<?php
/**
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
+---------------------------------------------------------------------------
*/

class feed extends MOX_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('动态管理'), "backend/feed/list/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(203));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`content` like "%' . $keyword . '%" )';
        }

        $list = $this->model('feed')->get_data_list($where, $_GET['page'], 20);

        TPL::assign('list', $list);

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/feed/index/'),
            'total_rows' => $this->model('feed')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('feed')->found_rows());

        TPL::output('backend/feed/list');
    }

    public function delete_action()
    {
        $id = $_GET['id'];

        $this->model('feed')->delete('feed', 'id = ' . trim($id));

        HTTP::redirect('/backend/feed/index/');

        exit;
    }

    public function recommend_action()
    {
        $id = $_GET['id'];

        $this->model('feed')->update('feed', array('is_home' => 1), 'id = ' . trim($id));

        HTTP::redirect('/backend/feed/index/');
        exit;
    }
}