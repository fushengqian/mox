<?php
class brand extends FARM_ADMIN_CONTROLLER
{
    /**
     * 品牌列表
     */
    public function index_action()
    {
        $this->crumb(FARM_APP::lang()->_t('品牌管理'), "admin/brand/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(100));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`name` like "%' . $keyword . '%" )';
        }

        $list = $this->model('brand')->get_data_list($where, $_GET['aid'], 20);

        TPL::assign('list', $list);

        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/brand/index/'),
            'total_rows' => $this->model('brand')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('brand')->found_rows());

        TPL::output('admin/brand/list');
    }

    /**
     * 编辑
     */
    public function edit_action()
    {
        $this->crumb(FARM_APP::lang()->_t('编辑品牌'), "admin/brand/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(101));

        TPL::import_js('ueditor/ueditor.config.js');
        TPL::import_js('ueditor/ueditor.all.min.js');
        TPL::import_js('ueditor/lang/zh-cn/zh-cn.js');

        if ($_GET['id']) {
            $info = $this->model('brand')->fetch_row('brand', 'id = '.intval($_GET['id']));
            TPL::assign('info', $info);
        }

        TPL::output('admin/brand/edit');
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

        H::ajax_json_output(FARM_APP::RSM(array(
            'url' => get_js_url('/admin/brand/edit/id-'.$id.'/')
        ), 1, null));
    }
}