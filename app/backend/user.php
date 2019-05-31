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

class user extends MOX_ADMIN_CONTROLLER
{
    public function list_action()
    {
        $where = array();

        if ($_GET['ip'] AND preg_match('/(\d{1,3}\.){3}(\d{1,3}|\*)/', $_GET['ip'])) {
            if (substr($_GET['ip'], -2, 2) == '.*') {
                $ip_base = ip2long(str_replace('.*', '.0', $_GET['ip']));

                if ($ip_base) {
                    $where[] = 'last_ip BETWEEN ' . $ip_base . ' AND ' . ($ip_base + 255);
                }
            } else {
                $ip_base = ip2long($_GET['ip']);

                if ($ip_base) {
                    $where[] = 'last_ip = ' . $ip_base;
                }
            }
        }

        if ($_GET['mobile']) {
            $where[] = 'phone = ' . $_GET['mobile'];
        }

        if ($_GET['name']) {
            $where[] = 'user_name = "' . $_GET['name'] . '"';
        }

        if ($_GET['email']) {
            $where[] = 'email = "' . $_GET['email'] . '"';
        }

        $is_us = $_GET['is_us'] ? intval($_GET['is_us']) : 0;
        if ($is_us == 1) {
            $where[] = 'is_us = 0';
        } else if ($is_us == 2) {
            $where[] = 'is_us = 1';
        }

        $order = !empty($_GET['order']) ? trim($_GET['order']) : 'id';

        $user_list = $this->model('user')->fetch_page('user', implode(' AND ', $where), $order . ' DESC', $_GET['page'], $this->per_page);

        $total_rows = $this->model('user')->found_rows();

        $url_param = array();

        foreach ($_GET as $key => $val) {
            if (!in_array($key, array('app', 'c', 'act', 'aid'))) {
                $url_param[] = $key . '-' . $val;
            }
        }

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/user/list/') . implode('__', $url_param).'__aid',
            'total_rows' => $total_rows,
            'per_page' => $this->per_page
        ))->create_links());

        $this->crumb(MOX_APP::lang()->_t('用户列表'), "backend/user/list/");

        TPL::assign('total_rows', $total_rows);
        TPL::assign('list', $user_list);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(401));
        TPL::assign('orderby', $order);
        TPL::assign('is_us', $is_us);

        TPL::output('backend/user/list');
    }

    /**
     * 禁用用户
     */
    public function delete_action()
    {
        $user_id = $_GET['id'] ? $_GET['id'] : 0;

        $result = $this->model('user')->delete('user', 'id = ' . intval($user_id));

        if ($result) {
            echo 'succeed...';
        } else {
            echo 'fail...';
        }

        exit;
    }

    /**
     * 设置为自己
     */
    public function isus_action()
    {
        $user_id = $_GET['id'] ? $_GET['id'] : 0;

        $result = $this->model('user')->update('user', array('is_us' => 1),'id = "'.intval($user_id).'"');

        if ($result) {
            echo 'succeed...';
        } else {
            echo 'fail...';
        }

        exit;
    }

    public function group_list_action()
    {
        $this->crumb(MOX_APP::lang()->_t('用户组管理'), "backend/user/group_list/");

        if (!$this->user_info['permission']['is_administortar']) {
            H::redirect_msg(MOX_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }

        TPL::assign('mem_group', $this->model('account')->get_user_group_list(1));
        TPL::assign('system_group', $this->model('account')->get_user_group_list(0, 0));
        TPL::assign('custom_group', $this->model('account')->get_user_group_list(0, 1));
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(501));
        TPL::output('backend/user/group_list');
    }

    public function group_edit_action()
    {
        $this->crumb(MOX_APP::lang()->_t('修改用户组'), "backend/user/group_list/");

        if (!$this->user_info['permission']['is_administortar']) {
            H::redirect_msg(MOX_APP::lang()->_t('你没有访问权限, 请重新登录'), '/');
        }

        if (!$group = $this->model('account')->get_user_group_by_id($_GET['group_id'])) {
            H::redirect_msg(MOX_APP::lang()->_t('用户组不存在'), '/backend/user/group_list/');
        }

        TPL::assign('group', $group);
        TPL::assign('group_pms', $group['permission']);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(501));
        TPL::output('backend/user/group_edit');
    }

    public function edit_action()
    {
        $this->crumb(MOX_APP::lang()->_t('编辑用户资料'), 'backend/user/edit/');

        if (!$user = $this->model('account')->get_user_info_by_uid($_GET['uid'], TRUE)) {
            H::redirect_msg(MOX_APP::lang()->_t('用户不存在'), '/backend/user/list/');
        }

        if ($user['group_id'] == 1 AND !$this->user_info['permission']['is_administortar']) {
            H::redirect_msg(MOX_APP::lang()->_t('你没有权限编辑管理员账号'), '/backend/user/list/');
        }

        TPL::assign('mem_group', $this->model('account')->get_user_group_by_id($user['reputation_group']));
        TPL::assign('system_group', $this->model('account')->get_user_group_list(0));
        TPL::assign('user', $user);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(500));

        TPL::output('backend/user/edit');
    }

    public function user_add_action()
    {
        $this->crumb(MOX_APP::lang()->_t('添加用户'), "backend/user/list/user_add/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(500));

        TPL::assign('system_group', $this->model('account')->get_user_group_list(0));

        TPL::output('backend/user/add');
    }

    public function invites_action()
    {
        $this->crumb(MOX_APP::lang()->_t('批量邀请'), "backend/user/invites/");

        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(502));
        TPL::output('backend/user/invites');
    }

    public function verify_approval_list_action()
    {
        $approval_list = $this->model('verify')->approval_list($_GET['page'], $_GET['status'], $this->per_page);

        $total_rows = $this->model('verify')->found_rows();

        foreach ($approval_list AS $key => $val) {
            if (!$uids[$val['uid']]) {
                $uids[$val['uid']] = $val['uid'];
            }
        }

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/user/verify_approval_list/status-' . $_GET['status']),
            'total_rows' => $total_rows,
            'per_page' => $this->per_page
        ))->create_links());

        $this->crumb(MOX_APP::lang()->_t('认证审核'), 'backend/user/verify_approval_list/');

        TPL::assign('users_info', $this->model('account')->get_user_info_by_uids($uids));
        TPL::assign('approval_list', $approval_list);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(501));

        TPL::output('backend/user/verify_approval_list');
    }

    public function register_approval_list_action()
    {
        if (get_setting('register_valid_type') != 'approval') {
            H::redirect_msg(MOX_APP::lang()->_t('未启用新用户注册审核'), '/backend/');
        }

        $user_list = $this->model('account')->fetch_page('users', 'group_id = 3', 'uid ASC', $_GET['page'], $this->per_page);

        $total_rows = $this->model('account')->found_rows();

        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/user/register_approval_list/'),
            'total_rows' => $total_rows,
            'per_page' => $this->per_page
        ))->create_links());

        $this->crumb(MOX_APP::lang()->_t('注册审核'), 'backend/user/register_approval_list/');

        TPL::assign('list', $user_list);
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(601));

        TPL::output('backend/user/register_approval_list');
    }

    public function verify_approval_edit_action()
    {
        if (!$verify_apply = $this->model('verify')->fetch_apply($_GET['id'])) {
            H::redirect_msg(MOX_APP::lang()->_t('审核认证不存在'), '/backend/user/register_approval_list/');
        }

        TPL::assign('verify_apply', $verify_apply);
        TPL::assign('user', $this->model('account')->get_user_info_by_uid($_GET['id']));
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(601));
        $this->crumb(MOX_APP::lang()->_t('编辑认证审核资料'), 'backend/user/verify_approval_list/');
        TPL::output('backend/user/verify_approval_edit');
    }

    public function integral_log_action()
    {
        if ($log = $this->model('integral')->fetch_page('integral_log', 'uid = ' . intval($_GET['uid']), 'time DESC', $_GET['page'], 50)) {
            TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
                'base_url' => get_js_url('/backend/user/integral_log/uid-' . intval($_GET['uid'])),
                'total_rows' => $this->model('integral')->found_rows(),
                'per_page' => 50
            ))->create_links());

            foreach ($log AS $key => $val) {
                $parse_items[$val['id']] = array(
                    'item_id' => $val['item_id'],
                    'action' => $val['action']
                );
            }

            TPL::assign('integral_log', $log);
            TPL::assign('integral_log_detail', $this->model('integral')->parse_log_item($parse_items));
        }

        TPL::assign('user', $this->model('account')->get_user_info_by_uid($_GET['uid']));
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(502));
        $this->crumb(MOX_APP::lang()->_t('积分日志'), '/backend/user/integral_log/uid-' . $_GET['uid']);
        TPL::output('backend/user/integral_log');
    }
}