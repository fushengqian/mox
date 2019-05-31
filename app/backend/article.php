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

if (!defined('IN_MOX'))
{
    die;
}

class article extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(102));
    }

    public function list_action()
    {
        if ($this->is_post())
        {
            foreach ($_POST as $key => $val)
            {
                if ($key == 'keyword' OR $key == 'user_name')
                {
                    $val = rawurlencode($val);
                }
                
                $param[] = $key . '-' . $val;
            }
            
            H::ajax_json_output(MOX_APP::RSM(array(
                'url' => get_js_url('/backend/article/list/' . implode('__', $param))
            ), 1, null));
        }
        
        $where = array();
        
        if ($_GET['keyword'])
        {
            $where[] = "(`title` LIKE '%" . $this->model('article')->quote($_GET['keyword']) . "%')";
        }

        if ($_GET['status'])
        {
            $where[] = "(`status` = '".intval($_GET['status'])."')";
        }
        if ($_GET['cate'])
        {
            $where[] = "(`cate` = '".trim($_GET['status'])."')";
        }
        
        if ($articles_list = $this->model('article')->fetch_page('article', implode(' AND ', $where), 'id DESC', $_GET['page'], $this->per_page))
        {
            $search_articles_total = $this->model('article')->found_rows();
        }
        
        if ($articles_list)
        {
            foreach ($articles_list AS $key => $val)
            {
                $articles_list[$key]['user_info'] = $this->model('user')->get_user_info_by_id($val['user_id']);
            }
        }
        
        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/article/list/'),
            'total_rows' => $search_articles_total,
            'per_page' => $this->per_page
        ))->create_links());
        
        $this->crumb(MOX_APP::lang()->_t('文章管理'), 'backend/article/list/');
        TPL::assign('articles_count', $search_articles_total);
        TPL::assign('list', $articles_list);
        TPL::assign('status', intval($_GET['status']));
        TPL::output('backend/article/list');
    }

    public function update_action()
    {
        $id = intval($_POST['id']);

        $type = intval($_POST['type']);

        if (empty($id)) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '系统错误~'));
        }

        if ($type == '1') {
            $result = $this->model('article')-> update('article', array('status' => 1, 'update_time' => time()), 'id = '.$id);
        } else {
            $result = $this->model('article')-> update('article', array('is_banner' => 1, 'update_time' => time()), 'id = '.$id);
        }

        if ($result) {
            H::ajax_json_output(MOX_APP::RSM(null, 1, '后台更新文章成功'));
        } else {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '抱歉，更新失败！'));
        }
    }

    /**
     * 删除
     */
    public function delete_action()
    {
        $id = intval($_REQUEST['id']);

        if (empty($id)) {
            exit('fail...');
        }

        $this->model('system')->delete('article', 'id IN (' . ($id) . ')');

        exit('success...');
    }
}
