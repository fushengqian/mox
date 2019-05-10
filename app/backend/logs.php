<?php

class logs extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }

    // 日志列表
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('日志列表'), "admin/logs/list/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(902));

        $where = '1 = 1';
        $title = $_GET['content'] ? trim($_GET['content']) : '';
        if ($title) {
            $where .= " AND content like '%" . $title . "%'";
        }

        $list = $this->model('logs')->get_data_list($where, $_GET['aid'], 20, 'id desc', true);

        TPL::assign('list', $list);

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/logs/index/'),
            'total_rows' => $this->model('logs')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('logs')->found_rows());

        TPL::output('admin/logs/index');
    }

    public function delete_action()
    {
        $id = trim($_POST['id']);

        if (empty($id)) {
            H::ajax_json_output('参数有误');
        }

        $this->model('system')->delete('logs', 'id IN (' . ($id) . ')');

        H::ajax_json_output(MOX_APP::RSM(array(), 1, '删除日志'));
    }
}
