<?php
define('IN_AJAX', TRUE);

class ajax extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }
    
    public function login_process_action()
    {
        if (get_setting('admin_login_seccode') == 'Y' AND !MOX_APP::captcha()->is_validate($_POST['seccode_verify']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请填写正确的验证码')));
        }
        
        if (get_setting('ucenter_enabled') == 'Y')
        {
            if (! $user_info = $this->model('ucenter')->login($this->user_info['email'], $_POST['password']))
            {
                $user_info = $this->model('user')->check_login($this->user_info['email'], $_POST['password']);
            }
        }
        else
        {
            $user_info = $this->model('user')->check_login($_POST['account'], $_POST['password']);
        }
        
        if ($user_info['id'])
        {
            $this->model('admin')->set_admin_login($user_info['id']);
            H::ajax_json_output(MOX_APP::RSM(array(
                'url' => $_POST['url'] ? base64_decode($_POST['url']) : get_js_url('/backend/')
            ), 1, null));
        }
        else
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('后台登录帐号或密码错误')));
        }
    }
    
    public function save_settings_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if ($_POST['upload_dir'])
        {
            $_POST['upload_dir'] = rtrim(trim($_POST['upload_dir']), '\/');
        }
        
        if ($_POST['upload_url'])
        {
            $_POST['upload_url'] = rtrim(trim($_POST['upload_url']), '\/');
        }
        
        if ($_POST['img_url'])
        {
            $_POST['img_url'] = rtrim(trim($_POST['img_url']), '\/');
        }
        
        if ($_POST['request_route_custom'])
        {
            $_POST['request_route_custom'] = trim($_POST['request_route_custom']);
            
            if ($request_routes = explode("\n", $_POST['request_route_custom']))
            {
                foreach ($request_routes as $key => $val)
                {
                    if (! strstr($val, '==='))
                    {
                        continue;
                    }
                    
                    list($m, $n) = explode('===', $val);
                    
                    if (substr($n, 0, 1) != '/' OR substr($m, 0, 1) != '/')
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('URL 自定义路由规则 URL 必须以 / 开头')));
                    }
                    
                    if (strstr($m, '/admin') OR strstr($n, '/admin'))
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('URL 自定义路由规则不允许设置 /admin 路由')));
                    }
                }
            }
        }
        
        if ($_POST['sensitive_words'])
        {
            $_POST['sensitive_words'] = trim($_POST['sensitive_words']);
        }
        
        $curl_require_setting = array('qq_login_enabled', 'sina_weibo_enabled');
        
        if (array_intersect(array_keys($_POST), $curl_require_setting))
        {
            foreach ($curl_require_setting AS $key)
            {
                if ($_POST[$key] == 'Y' AND !function_exists('curl_init'))
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('微博登录、QQ 登录等功能须服务器支持 CURL')));
                }
            }
        }
        
        if ($_POST['weixin_mp_token'])
        {
            $_POST['weixin_mp_token'] = trim($_POST['weixin_mp_token']);
        }
        
        if ($_POST['weixin_encoding_aes_key'])
        {
            $_POST['weixin_encoding_aes_key'] = trim($_POST['weixin_encoding_aes_key']);
            if (strlen($_POST['weixin_encoding_aes_key']) != 43)
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('微信公众平台接口 EncodingAESKey 应为 43 位')));
            }
        }
        
        if ($_POST['set_email_settings'])
        {
            $email_settings = array(
                'FOLLOW_ME' => 'N',
                'QUESTION_INVITE' => 'N',
                'NEW_ANSWER' => 'N',
                'NEW_MESSAGE' => 'N',
                'QUESTION_MOD' => 'N',
            );
            
            if ($_POST['new_user_email_setting'])
            {
                foreach ($_POST['new_user_email_setting'] AS $key => $val)
                {
                    unset($email_settings[$val]);
                }
            }
            $_POST['new_user_email_setting'] = $email_settings;
        }
        
        if ($_POST['slave_mail_config']['server'])
        {
            $_POST['slave_mail_config']['charset'] = $_POST['mail_config']['charset'];
        }
        
        if ($_POST['ucenter_path'])
        {
            $_POST['ucenter_path'] = rtrim(trim($_POST['ucenter_path']), '\/');
        }
        
        $this->model('setting')->set_vars($_POST);
        
        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('保存设置成功')));
    }
    
    public function approval_manage_action()
    {
        if (!in_array($_POST['batch_type'], array('approval', 'decline')))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('错误的请求')));
        }
        
        if ($_POST['approval_id'])
        {
            $_POST['approval_ids'] = array($_POST['approval_id']);
        }
        
        if (!$_POST['approval_ids'] OR !is_array($_POST['approval_ids']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择条目进行操作')));
        }
        
        switch ($_POST['type'])
        {
            case 'weibo_msg':
                if (get_setting('weibo_msg_enabled') != 'question')
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('导入微博消息至问题未启用')));
                }
                
                switch ($_POST['batch_type'])
                {
                    case 'approval':
                        $published_user = get_setting('weibo_msg_published_user');
                        
                        if (!$published_user['id'])
                        {
                            H::ajax_json_output(MOX_APP::RSM(MOX_APP::lang()->_t('微博发布用户不存在')));
                        }
                        
                        foreach ($_POST['approval_ids'] AS $approval_id)
                        {
                            $this->model('openid_weibo_weibo')->save_msg_info_to_question($approval_id, $published_user['id']);
                        }
                        
                        break;
                        
                    case 'decline':
                        foreach ($_POST['approval_ids'] AS $approval_id)
                        {
                            $this->model('openid_weibo_weibo')->del_msg_by_id($approval_id);
                        }
                        
                        break;
                }
                
                break;
                
            case 'received_email':
                $receiving_email_global_config = get_setting('receiving_email_global_config');
                
                if ($receiving_email_global_config['enabled'] != 'question')
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('导入邮件至问题未启用')));
                }
                
                switch ($_POST['batch_type'])
                {
                    case 'approval':
                        $receiving_email_global_config = get_setting('receiving_email_global_config');
                        
                        if (!$receiving_email_global_config['publish_user']['id'])
                        {
                            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('邮件发布用户不存在')));
                        }
                        
                        foreach ($_POST['approval_ids'] AS $approval_id)
                        {
                            $this->model('edm')->save_received_email_to_question($approval_id, $receiving_email_global_config['publish_user']['id']);
                        }
                        
                        break;
                        
                    case 'decline':
                        foreach ($_POST['approval_ids'] AS $approval_id)
                        {
                            $this->model('edm')->remove_received_email($approval_id);
                        }
                        break;
                }
                break;
            default:
                $func = $_POST['batch_type'] . '_publish';
                foreach ($_POST['approval_ids'] AS $approval_id)
                {
                    $this->model('publish')->$func($approval_id);
                }
                break;
        }
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function article_manage_action()
    {
        if (!$_POST['article_ids'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择文章进行操作')));
        }

        switch ($_POST['action'])
        {
            case 'del':
                foreach ($_POST['article_ids'] AS $article_id)
                {
                    $this->model('article')->remove_article($article_id);
                }

                H::ajax_json_output(MOX_APP::RSM(null, 1, null));
            break;
        }
    }

    public function save_category_sort_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        if (is_array($_POST['category']))
        {
            foreach ($_POST['category'] as $key => $val)
            {
                $this->model('category')->set_category_sort($key, $val['sort']);
            }
        }

        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('分类排序已自动保存')));
    }

    public function save_category_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        if ($_POST['category_id'] AND $_POST['parent_id'] AND $category_list = $this->model('system')->fetch_category('question', $_POST['category_id']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('系统允许最多二级分类, 当前分类下有子分类, 不能移动到其它分类')));
        }

        if (trim($_POST['title']) == '')
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入分类名称')));
        }

        if ($_POST['url_token'])
        {
            if (!preg_match("/^(?!__)[a-zA-Z0-9_]+$/i", $_POST['url_token']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('分类别名只允许输入英文或数字')));
            }

            if (preg_match("/^[\d]+$/i", $_POST['url_token']) AND ($_POST['category_id'] != $_POST['url_token']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('分类别名不可以全为数字')));
            }

            if ($this->model('category')->check_url_token($_POST['url_token'], $_POST['category_id']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('分类别名已经被占用请更换一个')));
            }
        }

        if ($_POST['category_id'])
        {
            $category_id = intval($_POST['category_id']);
        }
        else
        {
            $category_id = $this->model('category')->add_category('question', $_POST['title'], $_POST['parent_id']);
        }

        $category = $this->model('system')->get_category_info($category_id);

        if ($category['id'] == $_POST['parent_id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('不能设置当前分类为父级分类')));
        }

        $this->model('category')->update_category_info($category_id, $_POST['title'], $_POST['parent_id'], $_POST['url_token']);

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/category/list/')
        ), 1, null));
    }

    public function remove_category_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        if (intval($_POST['category_id']) == 1)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('默认分类不可删除')));
        }

        if ($this->model('category')->contents_exists($_POST['category_id']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('分类下存在内容, 请先批量移动问题到其它分类, 再删除当前分类')));
        }

        $this->model('category')->delete_category($_POST['category_id']);

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }

    public function move_category_contents_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        if (!$_POST['from_id'] OR !$_POST['target_id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请先选择指定分类和目标分类')));
        }

        if ($_POST['target_id'] == $_POST['from_id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('指定分类不能与目标分类相同')));
        }

        $this->model('category')->move_contents($_POST['from_id'], $_POST['target_id']);

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function save_nav_menu_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if ($_POST['nav_sort'])
        {
            if ($menu_ids = explode(',', $_POST['nav_sort']))
            {
                foreach($menu_ids as $key => $val)
                {
                    $this->model('menu')->update_nav_menu($val, array(
                        'sort' => $key
                    ));
                }
            }
        }
        
        if ($_POST['nav_menu'])
        {
            foreach($_POST['nav_menu'] as $key => $val)
            {
                $this->model('menu')->update_nav_menu($key, $val);
            }
        }
        
        $settings_var['category_display_mode'] = $_POST['category_display_mode'];
        $settings_var['nav_menu_show_child'] = isset($_POST['nav_menu_show_child']) ? 'Y' : 'N';
        
        $this->model('setting')->set_vars($settings_var);
        
        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('导航菜单保存成功')));
    }
    
    public function add_nav_menu_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        $title = trim($_POST['title']);
        $link = trim($_POST['link']);
        $parent_id = trim($_POST['parent_id']);
        
        if (!$title)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入导航标题')));
        }
        
        $this->model('menu')->add_nav_menu($title, $parent_id, $link);
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function remove_nav_menu_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        $this->model('menu')->remove_nav_menu($_POST['id']);
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function nav_menu_upload_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        MOX_APP::upload()->initialize(array(
            'allowed_types' => 'jpg,jpeg,png,gif',
            'upload_path' => get_setting('upload_dir') . '/nav_menu',
            'is_image' => TRUE,
            'file_name' => intval($_GET['id']) . '.jpg',
            'encrypt_name' => FALSE
        ))->do_upload('mox_upload_file');
        
        if (MOX_APP::upload()->get_error())
        {
            switch (MOX_APP::upload()->get_error())
            {
                default:
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('错误代码') . ': ' . MOX_APP::upload()->get_error()));
                break;
                
                case 'upload_invalid_filetype':
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('文件类型无效')));
                break;
            }
        }
        
        if (! $upload_data = MOX_APP::upload()->data())
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('上传失败, 请与管理员联系')));
        }
        
        if ($upload_data['is_image'] == 1)
        {
            MOX_APP::image()->initialize(array(
                'quality' => 90,
                'source_image' => $upload_data['full_path'],
                'new_image' => $upload_data['full_path'],
                'width' => 50,
                'height' => 50
            ))->resize();
        }
        
        $this->model('menu')->update_nav_menu($_GET['id'], array('icon' => basename($upload_data['full_path'])));
        
        echo htmlspecialchars(json_encode(array(
            'success' => true,
            'thumb' => get_setting('upload_url') . '/nav_menu/' . basename($upload_data['full_path'])
        )), ENT_NOQUOTES);
    }

    public function add_page_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if (!$_POST['url_token'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入页面 URL')));
        }
        
        if (!preg_match("/^(?!__)[a-zA-Z0-9_]+$/i", $_POST['url_token']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('页面 URL 只允许输入英文或数字')));
        }
        
        if ($this->model('page')->get_page_by_url_token($_POST['url_token']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('已经存在相同的页面 URL')));
        }
        
        $this->model('page')->add_page($_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['contents'], $_POST['url_token']);
        
        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/page/')
        ), 1, null));
    }
    
    public function remove_page_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        $this->model('page')->remove_page($_POST['id']);
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function edit_page_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if (!$page_info = $this->model('page')->get_page_by_url_id($_POST['page_id']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('页面不存在')));
        }
        
        if (!$_POST['url_token'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入页面 URL')));
        }
        
        if (!preg_match("/^(?!__)[a-zA-Z0-9_]+$/i", $_POST['url_token']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('页面 URL 只允许输入英文或数字')));
        }
        
        if ($_page_info = $this->model('page')->get_page_by_url_token($_POST['url_token']))
        {
            if ($_page_info['id'] != $_page_info['id'])
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('已经存在相同的页面 URL')));
            }
        }
        
        $this->model('page')->update_page($_POST['page_id'], $_POST['title'], $_POST['keywords'], $_POST['description'], $_POST['contents'], $_POST['url_token']);
        
        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/page/')
        ), 1, null));
    }

    public function save_page_status_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if ($_POST['page_ids'])
        {
            foreach ($_POST['page_ids'] AS $page_id => $val)
            {
                $this->model('page')->update_page_enabled($page_id, $_POST['enabled_status'][$page_id]);
            }
        }
        
        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('启用状态已自动保存')));
    }
    
    public function report_manage_action()
    {
        if (! $_POST['report_ids'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择内容进行操作')));
        }
        
        if (! $_POST['action_type'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择操作类型')));
        }
        
        if ($_POST['action_type'] == 'delete')
        {
            foreach ($_POST['report_ids'] as $val)
            {
                $this->model('question')->delete_report($val);
            }
        }
        else if ($_POST['action_type'] == 'handle')
        {
            foreach ($_POST['report_ids'] as $val)
            {
                $this->model('question')->update_report($val, array(
                    'status' => 1
                ));
            }
        }
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function save_user_group_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }
        
        if ($group_data = $_POST['group'])
        {
            foreach ($group_data as $key => $val)
            {
                if (!$val['group_name'])
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入用户组名称')));
                }
                
                if ($val['reputation_factor'])
                {
                    if (!is_digits($val['reputation_factor']) || floatval($val['reputation_factor']) < 0)
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('威望系数必须为大于或等于 0')));
                    }
                    
                    if (!is_digits($val['reputation_lower']) || floatval($val['reputation_lower']) < 0 || !is_digits($val['reputation_higer']) || floatval($val['reputation_higer']) < 0)
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('威望介于值必须为大于或等于 0')));
                    }
                    
                    $val['reputation_factor'] = floatval($val['reputation_factor']);
                }
                
                $this->model('user')->update_user_group_data($key, $val);
            }
        }
        
        if ($group_new = $_POST['group_new'])
        {
            foreach ($group_new['group_name'] as $key => $val)
            {
                if (trim($group_new['group_name'][$key]))
                {
                    $this->model('user')->add_user_group($group_new['group_name'][$key], 1, $group_new['reputation_lower'][$key], $group_new['reputation_higer'][$key], $group_new['reputation_factor'][$key]);
                }
            }
        }
        
        if ($group_ids = $_POST['group_ids'])
        {
            foreach ($group_ids as $key => $id)
            {
                $group_info = $this->model('user')->get_user_group_by_id($id);
                
                if ($group_info['custom'] == 1 OR $group_info['type'] == 1)
                {
                    $this->model('user')->delete_user_group_by_id($id);
                }
                else
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('系统用户组不可删除')));
                }
            }
        }
        
        MOX_APP::cache()->cleanGroup('users_group');
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function save_custom_user_group_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        if ($group_data = $_POST['group'])
        {
            foreach ($group_data as $key => $val)
            {
                if (!$val['group_name'])
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入用户组名称')));
                }

                $this->model('user')->update_user_group_data($key, $val);
            }
        }

        if ($group_new = $_POST['group_new'])
        {
            foreach ($group_new['group_name'] as $key => $val)
            {
                if (trim($group_new['group_name'][$key]))
                {
                    $this->model('user')->add_user_group($group_new['group_name'][$key], 0);
                }
            }
        }

        if ($group_ids = $_POST['group_ids'])
        {
            foreach ($group_ids as $key => $id)
            {
                $group_info = $this->model('user')->get_user_group_by_id($id);

                if ($group_info['custom'] == 1)
                {
                    $this->model('user')->delete_user_group_by_id($id);
                }
                else
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('系统用户组不可删除')));
                }
            }
        }

        MOX_APP::cache()->cleanGroup('users_group');

        if ($group_new OR $group_ids)
        {
            $rsm = array(
                'url' => get_js_url('/backend/user/group_list/r-' . rand(1, 999) . '#custom')
            );
        }

        H::ajax_json_output(MOX_APP::RSM($rsm, 1, null));
    }

    public function edit_user_group_permission_action()
    {
        if (!$this->user_info['permission']['is_administortar'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有访问权限, 请重新登录')));
        }

        $permission_array = array(
            'is_administortar',
            'is_moderator',
            'publish_question',
            'publish_approval',
            'publish_approval_time',
            'edit_question',
            'edit_topic',
            'manage_topic',
            'create_topic',
            'redirect_question',
            'upload_attach',
            'publish_url',
            'human_valid',
            'question_valid_hour',
            'answer_valid_hour',
            'visit_site',
            'visit_explore',
            'search_avail',
            'visit_question',
            'visit_topic',
            'visit_feature',
            'visit_people',
            'visit_chapter',
            'answer_show',
            'function_interval',
            'publish_article',
            'edit_article',
            'edit_question_topic',
            'publish_comment'
        );

        if (check_extension_package('ticket'))
        {
            $permission_array[] = 'is_service';

            $permission_array[] = 'publish_ticket';
        }

        if (check_extension_package('project'))
        {
            $permission_array[] = 'publish_project';
        }

        $group_setting = array();

        foreach ($permission_array as $permission)
        {
            if ($_POST[$permission])
            {
                $group_setting[$permission] = $_POST[$permission];
            }
        }

        $this->model('user')->update_user_group_data($_POST['group_id'], array(
            'permission' => serialize($group_setting)
        ));

        MOX_APP::cache()->cleanGroup('users_group');

        H::ajax_json_output(MOX_APP::RSM(null, 1, MOX_APP::lang()->_t('用户组权限已更新')));
    }

    public function save_user_action()
    {
        if ($_POST['id'])
        {
            if (!$user_info = $this->model('user')->get_user_info_by_id($_POST['id']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('用户不存在')));
            }

            if ($user_info['group_id'] == 1 AND !$this->user_info['permission']['is_administortar'])
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('你没有权限编辑管理员账号')));
            }

            if ($_POST['user_name'] != $user_info['user_name'] AND $this->model('user')->get_user_info_by_username($_POST['user_name']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('用户名已存在')));
            }

            if ($_POST['email'] != $user_info['email'] AND $this->model('user')->get_user_info_by_username($_POST['email']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('E-mail 已存在')));
            }
            
            if ($_POST['mobile'] != $user_info['mobile'] AND $this->model('user')->get_user_info_by_username($_POST['mobile']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('手机号已存在')));
            }

            if ($_FILES['user_avatar']['name'])
            {
                MOX_APP::upload()->initialize(array(
                    'allowed_types' => 'jpg,jpeg,png,gif',
                    'upload_path' => get_setting('upload_dir') . '/avatar/' . $this->model('user')->get_avatar($user_info['id'], '', 1),
                    'is_image' => TRUE,
                    'max_size' => get_setting('upload_avatar_size_limit'),
                    'file_name' => $this->model('user')->get_avatar($user_info['id'], '', 2),
                    'encrypt_name' => FALSE
                ))->do_upload('user_avatar');

                if (MOX_APP::upload()->get_error())
                {
                    switch (MOX_APP::upload()->get_error())
                    {
                        default:
                            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('错误代码') . ': ' . MOX_APP::upload()->get_error()));
                        break;

                        case 'upload_invalid_filetype':
                            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('文件类型无效')));
                        break;

                        case 'upload_invalid_filesize':
                            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('文件尺寸过大, 最大允许尺寸为 %s KB', get_setting('upload_size_limit'))));
                        break;
                    }
                }

                if (! $upload_data = MOX_APP::upload()->data())
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('上传失败, 请与管理员联系')));
                }

                if ($upload_data['is_image'] == 1)
                {
                    foreach(MOX_APP::config()->get('image')->avatar_thumbnail AS $key => $val)
                    {
                        $thumb_file[$key] = $upload_data['file_path'] . $this->model('user')->get_avatar($user_info['id'], $key, 2);

                        MOX_APP::image()->initialize(array(
                            'quality' => 90,
                            'source_image' => $upload_data['full_path'],
                            'new_image' => $thumb_file[$key],
                            'width' => $val['w'],
                            'height' => $val['h']
                        ))->resize();
                    }
                }

                $update_data['avatar_file'] = $this->model('user')->get_avatar($user_info['id'], null, 1) . basename($thumb_file['min']);
            }

            if ($_POST['email'])
            {
                $update_data['email'] = htmlspecialchars($_POST['email']);
            }

            $update_data['valid_email'] = intval($_POST['valid_email']);
            $update_data['forbidden'] = intval($_POST['forbidden']);

            $update_data['group_id'] = intval($_POST['group_id']);

            if ($update_data['group_id'] == 1 AND !$this->user_info['permission']['is_administortar'])
            {
                unset($update_data['group_id']);
            }

            $update_data['province'] = htmlspecialchars($_POST['province']);
            $update_data['city'] = htmlspecialchars($_POST['city']);
            $update_data['mobile'] = htmlspecialchars($_POST['mobile']);

            $update_data['sex'] = intval($_POST['sex']);

            $this->model('user')->update_users_fields($update_data, $user_info['id']);

            if ($_POST['delete_avatar'])
            {
                $this->model('user')->delete_avatar($user_info['id']);
            }

            if ($_POST['password'])
            {
                $this->model('user')->update_user_password_ingore_oldpassword($_POST['password'], $user_info['id'], fetch_salt(4));
            }

            if ($_POST['user_name'] != $user_info['user_name'])
            {
                $this->model('user')->update_user_name($_POST['user_name'], $user_info['id']);
            }

            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('用户资料更新成功')));
        }
        else
        {
            $_POST['user_name'] = trim($_POST['user_name']);

            $_POST['email'] = trim($_POST['email']);

            $_POST['password'] = trim($_POST['password']);

            $_POST['group_id'] = intval($_POST['group_id']);

            if (!$_POST['user_name'])
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入用户名')));
            }

            if ($this->model('user')->check_username($_POST['user_name']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('用户名已经存在')));
            }

            if ($this->model('user')->check_email($_POST['email']))
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('E-Mail 已经被使用, 或格式不正确')));
            }

            if (strlen($_POST['password']) < 6 or strlen($_POST['password']) > 16)
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('密码长度不符合规则')));
            }

            $id = $this->model('user')->user_register($_POST['user_name'], $_POST['password'], $_POST['email']);

            if ($_POST['group_id'] == 1 AND !$this->user_info['permission']['is_administortar'])
            {
                $_POST['group_id'] = 4;
            }

            if ($_POST['group_id'] != 4)
            {
                $this->model('user')->update('users', array(
                    'group_id' => $_POST['group_id'],
                ), 'id = ' . $id);
            }

            H::ajax_json_output(MOX_APP::RSM(array(
                'url' => get_js_url('/backend/user/list/')
            ), 1, null));
        }
    }

    public function forbidden_user_action()
    {
        $this->model('user')->forbidden_user_by_id($_POST['id'], $_POST['status'], $this->user_id);

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }

    public function send_invites_action()
    {
        if ($_POST['email_list'])
        {
            if ($emails = explode("\n", str_replace("\r", "\n", $_POST['email_list'])))
            {
                foreach($emails as $key => $email)
                {
                    if (!H::valid_email($email))
                    {
                        continue;
                    }

                    $email_list[] = strtolower($email);
                }
            }
        }
        else
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入邮箱地址')));
        }
        
        $this->model('invitation')->send_batch_invitations(array_unique($email_list), $this->user_id, $this->user_info['user_name']);
        
        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('邀请已发送')));
    }
    
    public function integral_process_action()
    {
        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择用户进行操作')));
        }
        
        if (!$_POST['note'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请填写理由')));
        }
        
        $this->model('integral')->process($_POST['id'], 'AWARD', $_POST['integral'], $_POST['note']);
        
        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/user/integral_log/id-' . $_POST['id'])
        ), 1, null));
    }
    
    public function register_approval_manage_action()
    {
        if (!is_array($_POST['approval_ids']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择条目进行操作')));
        }
        
        switch ($_POST['batch_type'])
        {
            case 'approval':
                foreach ($_POST['approval_ids'] AS $approval_id)
                {
                    if($approval_id)
                    {
                        $this->model('user') -> update('users', array('group_id' => 4,), 'id = ' . intval($approval_id));
                    }
                }
            break;
            
            case 'decline':
                foreach ($_POST['approval_ids'] AS $approval_id)
                {
                    if ($user_info = $this->model('user')->get_user_info_by_id($approval_id))
                    {
                        if ($user_info['email'])
                        {
                            $this->model('email')->action_email('REGISTER_DECLINE', $user_info['email'], null, array(
                                'message' => htmlspecialchars($_POST['reason'])
                            ));
                        }
                        
                        $this->model('system')->remove_user_by_id($approval_id, true);
                    }
                }
            break;
        }
        
        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function save_verify_approval_action()
    {
        if ($_POST['id'])
        {
            $this->model('verify')->update_apply($_POST['id'], $_POST['name'], $_POST['reason'], array(
                'id_code' => htmlspecialchars($_POST['id_code']),
                'contact' => htmlspecialchars($_POST['contact'])
            ));
        }

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/user/verify_approval_list/')
        ), 1, null));
    }

    public function verify_approval_manage_action()
    {
        if (!is_array($_POST['approval_ids']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择条目进行操作')));
        }

        switch ($_POST['batch_type'])
        {
            case 'approval':
            case 'decline':
                $func = $_POST['batch_type'] . '_verify';

                foreach ($_POST['approval_ids'] AS $approval_id)
                {
                    $this->model('verify')->$func($approval_id);
                }
            break;
        }

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }

    public function remove_user_action()
    {
        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('错误的请求')));
        }

        @set_time_limit(0);

        $user_info = $this->model('user')->get_user_info_by_id($_POST['id']);

        if (!$user_info)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('所选用户不存在')));
        }
        else
        {
            if ($user_info['group_id'] == 1)
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('不允许删除管理员用户组用户')));
            }

            $this->model('system')->remove_user_by_id($_POST['id'], $_POST['remove_user_data']);
        }

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/user/list/')
        ), 1, null));
    }

    public function remove_users_action()
    {
        if (!is_array($_POST['ids']) OR !$_POST['ids'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择要删除的用户')));
        }

        @set_time_limit(0);

        foreach ($_POST['ids'] AS $id)
        {
            $user_info = $this->model('user')->get_user_info_by_id($id);

            if ($user_info)
            {
                if ($user_info['group_id'] == 1)
                {
                    continue;
                }

                $this->model('system')->remove_user_by_id($id, true);
            }
            else
            {
                continue;
            }
        }

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }

    public function topic_statistic_action()
    {
        $topic_statistic = array();

        if ($topic_list = $this->model('topic')->get_hot_topics(null, $_GET['limit'], $_GET['tag']))
        {
            foreach ($topic_list AS $key => $val)
            {
                $topic_statistic[] = array(
                    'title' => $val['topic_title'],
                    'week' => $val['discuss_count_last_week'],
                    'month' => $val['discuss_count_last_month'],
                    'all' => $val['discuss_count']
                );
            }
        }

        echo json_encode($topic_statistic);
    }

    public function statistic_action()
    {
        if (!$start_time = strtotime($_GET['start_date']))
        {
            $start_time = strtotime('-12 months');
        }

        if (!$end_time = strtotime($_GET['end_date']))
        {
            $end_time = time();
        }

        if ($_GET['tag'])
        {
            $statistic_tag = explode(',', $_GET['tag']);
        }

        if (!$month_list = get_month_list($start_time, $end_time, 'y'))
        {
            die;
        }

        foreach ($month_list AS $key => $val)
        {
            $labels[] = $val['year'] . '-' . $val['month'];
            $data_template[] = 0;
        }

        if (!$statistic_tag)
        {
            die;
        }

        foreach ($statistic_tag AS $key => $val)
        {
            switch ($val)
            {
                case 'new_answer':  // 新增答案
                case 'new_question':    // 新增问题
                case 'new_user':    // 新注册用户
                case 'user_valid':  // 新激活用户
                case 'new_topic':   // 新增话题
                case 'new_answer_vote': // 新增答案投票
                case 'new_answer_thanks': // 新增答案感谢
                case 'new_favorite_item': // 新增收藏条目
                case 'new_question_thanks': // 新增问题感谢
                case 'new_question_redirect': // 新增问题重定向
                    $statistic[] = $this->model('system')->statistic($val, $start_time, $end_time);
                break;
            }
        }

        foreach($statistic AS $key => $val)
        {
            $statistic_data = $data_template;

            foreach ($val AS $k => $v)
            {
                $data_key = array_search($v['date'], $labels);

                $statistic_data[$data_key] = $v['count'];
            }

            $data[] = $statistic_data;

        }

        echo json_encode(array(
            'labels' => $labels,
            'data' => $data
        ));
    }

    public function weibo_batch_action()
    {
        if (!$_POST['action'] OR !isset($_POST['id']))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('错误的请求')));
        }

        if (in_array($_POST['action'], array('add_service_user', 'del_service_user')))
        {
            $user_info = $this->model('user')->get_user_info_by_id($_POST['id']);

            if (!$user_info)
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('所选用户不存在')));
            }

            $service_info = $this->model('openid_weibo_oauth')->get_weibo_user_by_id($user_info['id']);

            $tmp_service_account = MOX_APP::cache()->get('tmp_service_account');
        }

        switch ($_POST['action'])
        {
            case 'add_service_user':
                if ($service_info)
                {
                    if (isset($service_info['last_msg_id']))
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该用户已是回答用户')));
                    }

                    $this->model('openid_weibo_weibo')->update_service_account($user_info['id'], 'add');

                    $rsm = array('staus' => 'bound');
                }
                else
                {
                    if ($tmp_service_account[$user_info['id']])
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该用户已是回答用户')));
                    }

                    $tmp_service_account[$user_info['id']] = array(
                                                                    'id' => $user_info['id'],
                                                                    'user_name' => $user_info['user_name'],
                                                                    'url_token' => $user_info['url_token']
                                                                );

                    natsort($tmp_service_account);

                    MOX_APP::cache()->set('tmp_service_account', $tmp_service_account, 86400);

                    $rsm = array('staus' => 'unbound');
                }

                break;

            case 'del_service_user':
                if ($service_info)
                {
                    if (!isset($service_info['last_msg_id']))
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该用户不是回答用户')));
                    }

                    $this->model('openid_weibo_weibo')->update_service_account($user_info['id'], 'del');
                }
                else
                {
                    if (!$tmp_service_account[$user_info['id']])
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该用户不是回答用户')));
                    }

                    unset($tmp_service_account[$user_info['id']]);

                    MOX_APP::cache()->set('tmp_service_account', $tmp_service_account, 86400);
                }

                break;

            case 'add_published_user':
                $weibo_msg_published_user = get_setting('weibo_msg_published_user');

                if ($_POST['id'] != $weibo_msg_published_user['id'])
                {
                    $published_user_info = $this->model('user')->get_user_info_by_id($_POST['id']);

                    if (!$published_user_info)
                    {
                        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('所选用户不存在')));
                    }

                    $this->model('setting')->set_vars(array(
                        'weibo_msg_published_user' => array(
                            'id' => $published_user_info['id'],
                            'user_name' => $published_user_info['user_name'],
                            'url_token' => $published_user_info['url_token']
                    )));
                }

                break;

            case 'weibo_msg_enabled':
                if (in_array($_POST['id'], array('question', 'ticket', 'N')))
                {
                    $this->model('setting')->set_vars(array(
                        'weibo_msg_enabled' => $_POST['id']
                    ));
                }

                break;
        }

        H::ajax_json_output(MOX_APP::RSM($rsm, 1, null));
    }

    public function save_approval_item_action()
    {
        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择待审项')));
        }

        if (!$_POST['type'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('类型不能为空')));
        }

        switch ($_POST['type'])
        {
            case 'weibo_msg':
                $approval_item = $this->model('openid_weibo_weibo')->get_msg_info_by_id($_POST['id']);

                if ($approval_item['question_id'])
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该消息已通过审核')));
                }

                $approval_item['type'] = 'weibo_msg';

                break;

            case 'received_email':
                $approval_item = $this->model('edm')->get_received_email_by_id($_POST['id']);

                if ($approval_item['question_id'])
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该邮件已通过审核')));
                }

                $approval_item['type'] = 'received_email';

                break;

            default:
                $approval_item = $this->model('publish')->get_approval_item($_POST['id']);

                break;
        }

        if (!$approval_item)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('待审项不存在')));
        }

        if ($_POST['type'] != $approval_item['type'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('类型不正确')));
        }

        if (!$_POST['title'] AND in_array($_POST['type'], array('question', 'article', 'received_email')))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入标题')));
        }

        if (!$_POST['content'] AND in_array($_POST['type'], array('answer', 'article_comment')))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入内容')));
        }

        switch ($approval_item['type'])
        {
            case 'question':
                $approval_item['data']['question_content'] = htmlspecialchars_decode($_POST['title']);

                $approval_item['data']['question_detail'] = htmlspecialchars_decode($_POST['content']);

                $approval_item['data']['topics'] = explode(',', htmlspecialchars_decode($_POST['topics']));

                break;

            case 'answer':
                $approval_item['data']['answer_content'] = htmlspecialchars_decode($_POST['content']);

                break;

            case 'article':
                $approval_item['data']['title'] = htmlspecialchars_decode($_POST['title']);

                $approval_item['data']['message'] = htmlspecialchars_decode($_POST['content']);

                break;

            case 'article_comment':
                $approval_item['data']['message'] = htmlspecialchars_decode($_POST['content']);

                break;

            case 'weibo_msg':
                $approval_item['text'] = htmlspecialchars_decode($_POST['content']);

                $approval_item['data']['attach_access_key'] = $approval_item['access_key'];

                break;

            case 'received_email':
                $approval_item['subject'] = htmlspecialchars_decode($_POST['title']);

                $approval_item['content'] = htmlspecialchars_decode($_POST['content']);

                break;
        }

        if ($approval_item['type'] != 'article_comment' AND $_POST['remove_attachs'])
        {
            foreach ($_POST['remove_attachs'] AS $attach_id)
            {
                $this->model('publish')->remove_attach($attach_id, $approval_item['data']['attach_access_key']);
            }
        }

        switch ($approval_item['type'])
        {
            case 'weibo_msg':
                $this->model('openid_weibo_weibo')->update('weibo_msg', array(
                    'text' => $approval_item['text']
                ), 'id = ' . $approval_item['id']);

                break;

            case 'received_email':
                $this->model('edm')->update('received_email', array(
                    'subject' => $approval_item['subject'],
                    'content' => $approval_item['content']
                ), 'id = ' . $approval_item['id']);

                break;

            default:
                $this->model('publish')->update('approval', array(
                    'data' => serialize($approval_item['data'])
                ), 'id = ' . $approval_item['id']);

                break;
        }

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => get_js_url('/backend/approval/list/')
        ), 1, null));
    }

    public function save_today_topics_action()
    {
        $today_topics = trim($_POST['today_topics']);

        $this->model('setting')->set_vars(array('today_topics' => $today_topics));

        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('设置已保存')));
    }

    public function save_receiving_email_config_action()
    {
        if ($_POST['id'])
        {
            $receiving_email_config = $this->model('edm')->get_receiving_email_config_by_id($_POST['id']);

            if (!$receiving_email_config)
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('该账号不存在')));
            }
        }

        $_POST['server'] = trim($_POST['server']);

        if (!$_POST['server'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入服务器地址')));
        }

        if (!$_POST['protocol'] OR !in_array($_POST['protocol'], array('pop3', 'imap')))
        {
             H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择协议')));
        }

        if ($_POST['port'] AND (!is_digits($_POST['port']) OR $_POST['port'] < 0 OR $_POST['port'] > 65535))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请输入有效的端口号（0 ~ 65535）')));
        }

        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择此账号对应的用户')));
        }

        $user_info = $this->model('user')->get_user_info_by_id($_POST['id']);

        if (!$user_info)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('所选用户不存在')));
        }

        $receiving_email_config = array(
                                        'server' => $_POST['server'],
                                        'protocol' => $_POST['protocol'],
                                        'ssl' => ($_POST['ssl'] == '1') ? '1' : '0',
                                        'username' => trim($_POST['username']),
                                        'password' => trim($_POST['password']),
                                        'id' => $user_info['id']
                                    );

        if ($_POST['port'])
        {
            $receiving_email_config['port'] = $_POST['port'];
        }

        if ($_POST['id'])
        {
            $this->model('edm')->update_receiving_email_config($_POST['id'], 'update', $receiving_email_config);

            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('保存设置成功')));
        }
        else
        {
            $config_id = $this->model('edm')->update_receiving_email_config(null, 'add', $receiving_email_config);

            H::ajax_json_output(MOX_APP::RSM(array(
                'url' => get_js_url('/backend/edm/receiving/id-' . $config_id)
            ), 1, null));
        }
    }

    public function save_receiving_email_global_config_action()
    {
        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请设置邮件内容对应提问用户')));
        }

        $user_info = $this->model('user')->get_user_info_by_id($_POST['id']);

        if (!$user_info)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('所选用户不存在')));
        }

        $this->model('setting')->set_vars(array(
            'receiving_email_global_config' => array(
                'enabled' => (in_array($_POST['enabled'], array('question', 'ticket'))) ? $_POST['enabled'] : 'N',
                'publish_user' => array(
                    'id' => $user_info['id'],
                    'user_name' => $user_info['user_name'],
                    'url_token' => $user_info['url_token']
            )
        )));

        H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('保存设置成功')));
    }

    public function remove_receiving_account_action()
    {
        if (!$_POST['id'])
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('请选择要删除的账号')));
        }

        $this->model('edm')->delete('receiving_email_config', 'id = ' . intval($_POST['id']));

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }
    
    public function attach_upload_action()
    {
        $item_type = $_GET['id'];

        MOX_APP::upload()->initialize(array(
        'allowed_types' => get_setting('allowed_upload_types'),
        'upload_path' => get_setting('upload_dir') . '/' . $item_type . '/' . gmdate('Ymd'),
        'is_image' => FALSE,
        'max_size' => get_setting('upload_size_limit')
        ));
        
        if (isset($_GET['mox_upload_file']))
        {
            MOX_APP::upload()->do_upload($_GET['mox_upload_file'], file_get_contents('php://input'));
        }
        else if (isset($_FILES['mox_upload_file']))
        {
            MOX_APP::upload()->do_upload('mox_upload_file');
        }
        else
        {
            return false;
        }
        
        if (MOX_APP::upload()->get_error())
        {
            switch (MOX_APP::upload()->get_error())
            {
                default:
                    die("{'error':'错误代码: " . MOX_APP::upload()->get_error() . "'}");
                    break;
                    
                case 'upload_invalid_filetype':
                    die("{'error':'文件类型无效'}");
                    break;
                   
                case 'upload_invalid_filesize':
                    die("{'error':'文件尺寸过大, 最大允许尺寸为 " . get_setting('upload_size_limit') .  " KB'}");
                    break;
            }
        }
        
        if (! $upload_data = MOX_APP::upload()->data())
        {
            die("{'error':'上传失败, 请与管理员联系'}");
        }
        
        if ($upload_data['is_image'] == 1)
        {
           foreach (MOX_APP::config()->get('image')->attachment_thumbnail AS $key => $val)
           {
               $thumb_file[$key] = $upload_data['file_path'] . $val['w'] . 'x' . $val['h'] . '_' . basename($upload_data['full_path']);
               MOX_APP::image()->initialize(array(
               'quality' => 120,
               'source_image' => $upload_data['full_path'],
               'new_image' => $thumb_file[$key],
               'width' => $val['w'],
               'height' => $val['h']
               ))->resize();
           }
        }
        
        $size = 'admin';
        
        $upload_data['thumb'] = get_setting('upload_url') . '/' . $item_type . '/' . gmdate('Ymd') . '/'.MOX_APP::config()->get('image')->attachment_thumbnail[$size]['w'] . 'x' . MOX_APP::config()->get('image')->attachment_thumbnail[$size]['h'] . '_' . $upload_data['file_name'];
        
        $upload_data['delete_url'] = 'admin/ajax/dinner/images_delete/?file_name='.$upload_data['file_name'];
        
        exit(htmlspecialchars(json_encode($upload_data), ENT_NOQUOTES));
    }
}
