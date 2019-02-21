<?php
class action extends FARM_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }

    // 日志列表
    public function index_action()
    {
        $this->crumb(FARM_APP::lang()->_t('用户行为'), "admin/action/list/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(901));

        $where = '1 = 1';
        $title = $_GET['content'] ? trim($_GET['content']) : '';
        if ($title) {
            $where .= " AND content like '%" . $title . "%'";
        }

        $list = $this->model('action')->get_data_list($where, $_GET['aid'], 20, 'id desc', true);

        TPL::assign('list', $list);

        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/action/index/'),
            'total_rows' => $this->model('action')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('logs')->found_rows());

        TPL::output('admin/action/index');
    }

    public function delete_action()
    {
        $id = trim($_POST['id']);

        if (empty($id)) {
            H::ajax_json_output('参数有误');
        }

        $this->model('system')->delete('action', 'id IN (' . ($id) . ')');

        H::ajax_json_output(FARM_APP::RSM(array(), 1, '删除日志'));
    }
}
