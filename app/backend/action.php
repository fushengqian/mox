<?php
/**
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

class action extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }

    // 日志列表
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('用户行为'), "backend/action/list/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(701));

        $where = '1 = 1';
        $title = $_GET['content'] ? trim($_GET['content']) : '';
        if ($title) {
            $where .= " AND content like '%" . $title . "%'";
        }

        $user_id = $_GET['user_id'] ? trim($_GET['user_id']) : '';
        if ($user_id) {
            $where .= " AND user_id = '" . $user_id . "'";
        }

        $list = $this->model('action')->get_data_list($where, $_GET['page'], 20, 'id desc', true);

        TPL::assign('list', $list);

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/action/index/'),
            'total_rows' => $this->model('action')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('action')->found_rows());

        TPL::output('backend/action/index');
    }

    public function delete_action()
    {
        $id = trim($_POST['id']);

        if (empty($id)) {
            H::ajax_json_output('参数有误');
        }

        $this->model('system')->delete('action', 'id IN (' . ($id) . ')');

        H::ajax_json_output(MOX_APP::RSM(array(), 1, '删除日志'));
    }
}
