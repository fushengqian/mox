<?php
class brand extends MOX_ADMIN_CONTROLLER
{
    /**
     * 品牌列表
     */
    public function index_action()
    {
        $this->crumb(MOX_APP::lang()->_t('品牌管理'), "backend/brand/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(100));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`name` like "%' . $keyword . '%" )';
        }

        $list = $this->model('brand')->get_data_list($where, $_GET['aid'], 20);

        TPL::assign('list', $list);

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/brand/index/'),
            'total_rows' => $this->model('brand')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('brand')->found_rows());

        TPL::output('backend/brand/list');
    }

    /**
     * 编辑
     */
    public function edit_action()
    {
        $this->crumb(MOX_APP::lang()->_t('编辑品牌'), "backend/brand/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(101));

        TPL::import_js('ueditor/ueditor.config.js');
        TPL::import_js('ueditor/ueditor.all.min.js');
        TPL::import_js('ueditor/lang/zh-cn/zh-cn.js');

        if ($_GET['id']) {
            $info = $this->model('brand')->fetch_row('brand', 'id = '.intval($_GET['id']));
            TPL::assign('info', $info);
        }

        TPL::output('backend/brand/edit');
    }

    /**
     * 保存
     */
    public function save_action()
    {
        $arr = array('name' => trim($_POST['name']),
                     'ename' => trim($_POST['ename']),
                     'area' => trim($_POST['area']),
                     'intro' => trim($_POST['intro']),
                     'website' => trim($_POST['website']));

        if ($_POST['id']) {
            $id = intval($_POST['id']);
            $this -> model('brand') -> update('brand', $arr, 'id = '.$id);
        } else {
            $id = $this -> model('brand') -> insert('brand', $arr);
        }

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/brand/edit/id-'.$id.'/')
        ), 1, null));
    }
}