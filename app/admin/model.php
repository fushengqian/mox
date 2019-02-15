<?php

class model extends FARM_ADMIN_CONTROLLER
{
    /**
     * 模型列表
     */
    public function index_action()
    {
        $this->crumb(FARM_APP::lang()->_t('模型管理'), "admin/model/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(101));

        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';

        $where = '1 = 1';
        if ($keyword) {
            $where .= ' AND (`name` like "%' . $keyword . '%" )';
        }

        $list = $this->model('model')->get_data_list($where, $_GET['aid'], 20);
        $brand_list = $this->model('brand')->fetch_all('brand');

        foreach ($list as $key => $val) {
            foreach ($brand_list as $k => $v) {
                if ($v['id'] == $val['brand_id']) {
                    $list[$key]['brand_name'] = $v['name'];
                }
            }
        }

        TPL::assign('list', $list);

        TPL::assign('pagination', FARM_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/admin/model/index/'),
            'total_rows' => $this->model('model')->found_rows(),
            'per_page' => 20
        ))->create_links());

        TPL::assign('total_rows', $this->model('model')->found_rows());

        TPL::output('admin/model/index');
    }

    /**
     * 删除
     */
    public function delete_action()
    {
        $id = $_GET['id'];

        $this->model('topic')->delete('model', 'id = ' . trim($id));

        HTTP::redirect('/admin/model/index/');
        exit;
    }

    /**
     * 编辑
     */
    public function edit_action()
    {
        $model_id = $_GET['id'] ? trim($_GET['id']) : 0;
        $this->crumb(FARM_APP::lang()->_t('编辑模型'), "admin/model/index/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(101));

        TPL::import_js('ueditor/ueditor.config.js');
        TPL::import_js('ueditor/ueditor.all.min.js');
        TPL::import_js('ueditor/lang/zh-cn/zh-cn.js');

        $brand_list = $this->model('brand')->fetch_all('brand');
        TPL::assign('brand_list', $brand_list);

        if ($model_id) {
            $info = $this->model('model')->fetch_row('model', 'id = ' . ($model_id));
            TPL::assign('info', $info);
        }

        TPL::output('admin/model/edit');
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
                     'code' => trim($_POST['code']),
                     'brand_id' => trim($_POST['brand_id']),
                     'scale' => trim($_POST['scale']),
                     'publish' => trim($_POST['publish']),
                     'update_time' => time(),
                     'website' => trim($_POST['website']));

        if ($_POST['id']) {
            $id = trim($_POST['id']);
            $this->model('model')->update('model', $arr, 'id = "'.$id.'"');
        } else {
            $arr['id'] = setId();
            $arr['create_time'] = time();
            $id = $this->model('model')->insert('model', $arr);
        }

        H::ajax_json_output(FARM_APP::RSM(array(
            'url' => get_js_url('/admin/model/edit/id-' . $id . '/')
        ), 1, null));
    }

    /**
     * 编辑图片
     */
    public function image_edit_action()
    {
        empty($images_list) && $images_list = array();

        $info = $this->model('model')->fetch_row('model', 'id = ' . trim($_GET['id']));
        TPL::assign('info', $info);

        $images_list = json_decode($info['pics'], $info['pics']);
        TPL::assign('images_list', $images_list);

        TPL::import_js('admin/js/fileupload.js');

        TPL::import_js('js/md5.js');

        TPL::assign('id', $_GET['id']);
        TPL::assign('info', $info);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(101));

        $this->crumb(FARM_APP::lang()->_t($info['name'] . '图片'), "/admin/model/index/");

        TPL::output('admin/model/images_edit');
    }

    /**
     * 删除照片
     */
    public function image_delete_action()
    {
        $url = trim($_POST['url']);
        $model_id = trim($_POST['model_id']);

        if (empty($model_id) || empty($url)) {
            H::ajax_json_output('参数有误');
        }

        $info = $this->model('model')->fetch_row('model', "id = '" . ($model_id) . "'");
        $pics = json_decode($info['pics'], true);
        $arr = [];
        foreach ($pics as $k => $v) {
           if ($v['url'] != $url) {
               $arr[] = $v;
           }
        }

        $this->model('model')->update('model', array('pics' => json_encode($arr)), 'id = "'.$model_id.'"');

        H::ajax_json_output(FARM_APP::RSM(array(), 1, null));
    }

    /**
     * 设置封面
     */
    public function image_preview_action()
    {
        $url = trim($_POST['url']);
        $model_id = trim($_POST['model_id']);

        if (empty($model_id) || empty($url)) {
            H::ajax_json_output('参数有误');
        }

        $this->model('model')->update('model', array('preview' => $url, 'update_time' => time()), 'id = "'.$model_id.'"');

        H::ajax_json_output(FARM_APP::RSM(array(), 1, null));
    }

    /**
     * 批量设置图片文字
     */
    public function image_brief_action()
    {
        $model_id = intval($_POST['model_id']);
        $para = trim($_POST['para']);

        if (empty($para) || empty($model_id)) {
            H::ajax_json_output('参数有误');
        }

        $ids = explode(',', $para);

        if (empty($ids)) {
            H::ajax_json_output('参数有误');
        }

        $info = $this->model('model')->fetch_row('model', "id = '" . ($model_id) . "'");
        $pics = json_decode($info['pics'], true);
        foreach ($pics as $k => $v) {
            foreach ($ids as $val) {
                $data = explode('_', $val);
                if ($data[0] && $data[1]) {
                    if ($v['url'] == $data[1]) {
                        $pics[$k]['brief'] = $data[0];
                    }
                }
            }
        }

        $this->model('model')->update('model', array('pics' => json_encode($pics), 'update_time' => time()), 'id = "'.$model_id.'"');

        H::ajax_json_output(FARM_APP::RSM(array(), 1, null));
    }
}