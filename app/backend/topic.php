<?php
class topic extends MOX_ADMIN_CONTROLLER
{
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('话题管理'), "backend/topic/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(202));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`content` like "%' . $keyword . '%" )';
        }

        $list = $this->model('topic')->get_data_list($where, $_GET['page'], 20);

        TPL::assign('list', $list);

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/topic/index/'),
            'total_rows' => $this->model('topic')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('topic')->found_rows());

        TPL::output('backend/topic/list');
    }

    public function edit_action()
    {
        $this->crumb(MOX_APP::lang()->_t('编辑话题'), "backend/topic/index/");

        $id = trim($_GET['id']);
        if ($id) {
            $info = $this->model('model')->fetch_row('topic', 'id = '.($id));
            TPL::assign('info', $info);
        }

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(202));
        TPL::output('backend/topic/edit');
    }

    public function save_action()
    {
        $id = trim($_POST['id']);

        if (empty($id)) {
            H::ajax_json_output('ID不能为空！');
        }

        if (empty($_POST['title'])) {
            H::ajax_json_output('标题不能为空！');
        }

        $arr = array('title' => trim($_POST['title']),
                     'preview' => trim($_POST['image']),
                     'update_time' => time());

        $this->model('topic')->update('topic', $arr, 'id = "'.$id.'"');

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/topic/edit/id-' .$id)
        ), 1, null));
    }

    public function delete_action()
    {
        $id = $_GET['id'];

        $this->model('topic')->delete('feed', 'id = ' . trim($id));

        HTTP::redirect('/backend/topic/index/');
        exit;
    }

    public function recommend_action()
    {
        $id = $_GET['id'];

        $this->model('topic')->update('topic', array('is_home' => 1), 'id = ' . trim($id));

        HTTP::redirect('/backend/topic/index/');
        exit;
    }
}