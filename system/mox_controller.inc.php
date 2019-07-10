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
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class MOX_CONTROLLER
{
    public $user_id;
    public $user_info;

    public function jsonReturn($result = array(), $code = 1, $message = 'SUCCESS', $notice = null, $time = '')
    {
        if (!empty($this->user_info)) {
            $notice = MOX_APP::model("notice")->get_notice($this->user_info['uid']);
        }

        $ret = array('code' => $code, 'message' => $message, 'result' => $result, 'notice' => $notice, 'time' => $time);

        echo json_encode($ret);
        exit;
    }
    
    public function __construct($process_setup = true)
    {
        // 获取当前用户 User ID
        $this->user_id = MOX_APP::user()->get_info('uid');

        if ($this->user_info = $this->model('user')->get_user_info_by_id($this->user_id))
        {
            $user_group = $this->model('user')->get_user_group($this->user_info['group_id'], $this->user_info['reputation_group']);

            if ($this->user_info['default_timezone'])
            {
                date_default_timezone_set($this->user_info['default_timezone']);
            }
        }
        else if ($this->user_id)
        {
            $this->model('user')->logout();
        }
        else
        {
            $user_group = $this->model('user')->get_user_group_by_id(99);

            if ($_GET['fromuid'])
            {
                HTTP::set_cookie('fromuid', $_GET['fromuid']);
            }
        }

        $this->user_info['group_name'] = $user_group['group_name'];
        $this->user_info['permission'] = $user_group['permission'];
        $this->user_info['uid'] = $this->user_id;

        MOX_APP::session()->permission = $this->user_info['permission'];

        if ($this->user_info['forbidden'] == 1)
        {
            $this->model('account')->logout();
            H::redirect_msg(MOX_APP::lang()->_t('抱歉, 你的账号已经被禁止登录'), '/');
        }
        else
        {
            TPL::assign('user_id', $this->user_id);
            TPL::assign('user_info', $this->user_info);
        }

        if ($this->user_id and ! $this->user_info['permission']['human_valid'])
        {
            unset(MOX_APP::session()->human_valid);
        }
        else if ($this->user_info['permission']['human_valid'] and ! is_array(MOX_APP::session()->human_valid))
        {
            MOX_APP::session()->human_valid = array();
        }

        //PC端导航菜单
        if (MOX_APP::$settings['pc_nav']) {
            TPL::assign('pc_nav', MOX_APP::$settings['pc_nav']);
        }

        //移动端导航菜单
        if (MOX_APP::$settings['mobile_nav']) {
            TPL::assign('mobile_nav', MOX_APP::$settings['mobile_nav']);
        }

        //引入系统CSS文件
        TPL::import_css(array(
            'css/common.css',
            'css/base.css',
            'emoji/jquery.mCustomScrollbar.min.css',
            'emoji/jquery.emoji.css',
            'emoji/railscasts.css'
        ));

        TPL::import_js(array(
            'js/jquery.min.js',
            'emoji/highlight.pack.js',
            'emoji/jquery.mousewheel-3.0.6.min.js',
            'emoji/jquery.mCustomScrollbar.min.js',
            'emoji/jquery.emoji.js',
            'js/common.js',
        ));

        $unread_msg_count = 0;
        if (!empty($this->user_id))
        {
            $unread_msg_count = MOX_APP::model("message") -> count('message', 'user_id = '.intval($this->user_id).' AND is_read = 0');
        }
        TPL::assign('unread_msg', intval($unread_msg_count));
        TPL::assign('user_info', $this->user_info);

        define('G_URL', G_DEMAIN);
        
        $this -> setup();
    }
    
    /**
     * 控制器 Setup 动作
     *
     * 每个继承于此类库的控制器均会调用此函数
     *
     * @access    public
     */
    public function setup() {}
    
    /**
     * 判断当前访问类型是否为 POST
     *
     * 调用 $_SERVER['REQUEST_METHOD']
     *
     * @access    public
     * @return    boolean
     */
    public function is_post()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * 调用系统 Model
     *
     * 于控制器中使用 $this->model('class')->function() 进行调用
     *
     * @access    public
     * @param    string
     * @return    object
     */
    public function model($model = null)
    {
        return MOX_APP::model($model);
    }
    
    /**
     * 输出meta location
     * */
    public function meta_location($city_info, $address = '', $coord = '')
    {
        if (!$city_info)
        {
            return;
        }
        
        $province = $this -> model('system') -> get_province_by_id($city_info['provid']);
        
        if (!$coord)
        {
            if (empty($city_info['coord']))
            {
                if ($address)
                {
                    $location = get_point($address);
                }
                else
                {
                    $location = get_point($city_info['name']);
                }
                
                if ($location)
                {
                    $coord = round($location['lng'], 6).','.round($location['lat'], 6);
                    if (!$address)
                    {
                        $this -> model('system') -> update('city', array('coord' => $coord), 'id = '.intval($city_info['id']));
                    }
                }
            }
            else
            {
                $coord = $city_info['coord'];
            }
        }
        
        if ($coord)
        {
            $meta = array(array('name' => 'location', 'content' => 'province='.$province['piovname'].';city='.$city_info['name'].';coord='.$coord));
        }
        else
        {
            $meta = array(array('name' => 'location', 'content' => 'province='.$province['piovname'].';city='.$city_info['name']));
        }
        
        TPL::assign('meta', $meta);
    }
}

/**
 * Mox 后台控制器
 *
 * @package     Mox
 * @subpackage  System
 * @category    Libraries
 * @author      Mox Dev Team
 */
class MOX_ADMIN_CONTROLLER extends MOX_CONTROLLER
{
    public $per_page = 20;
    
    public function __construct()
    {
        parent::__construct(false);
        
        if ($_GET['app'] != 'backend')
        {
            return false;
        }
        
        TPL::import_clean();
        
        TPL::import_js(array(
            'js/jquery.min.js',
            'dist/jquery/fileupload/jquery.ui.widget.js',
            'dist/jquery/fileupload/jquery.iframe-transport.js',
            'dist/jquery/fileupload/jquery.fileupload.js',
            'backend/js/mox_admin.js',
            'backend/js/mox_admin_template.js',
            'js/jquery.form.js',
            'backend/js/framework.js',
            'backend/js/global.js',
        ));

        TPL::import_css(array(
            'fonts/iconfont-backend.css',
            'backend/css/common.css'
        ));
        
        if (in_array($_GET['act'], array(
            'login',
            'login_process',
        )))
        {
            return true;
        }
        
        if ($admin_info = H::decode_hash(MOX_APP::session()->admin_login))
        {
            if (!$admin_info['uid'])
            {
                unset(MOX_APP::session()->admin_login);
                
                if ($_POST['_post_type'] == 'ajax')
                {
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('会话超时, 请重新登录')));
                }
                else
                {
                    H::redirect_msg(MOX_APP::lang()->_t('会话超时, 请重新登录'), '/backend/login/url-' . base64_encode($_SERVER['REQUEST_URI']));
                }
            }
        }
        else
        {
            if ($_POST['_post_type'] == 'ajax')
            {
                H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('会话超时, 请重新登录')));
            }
            else
            {
                HTTP::redirect('/backend/login/url-' . base64_encode($_SERVER['REQUEST_URI']));
            }
        }
        
        $this -> setup();
    }
    
    public function crumb($name, $url = null)
    {
        $this->_crumb(htmlspecialchars_decode($name), $url);
    }
    
    public function _crumb($name, $url = null)
    {
        if (is_array($name))
        {
            foreach ($name as $key => $value)
            {
                $this->crumb($key, $value);
            }

            return $this;
        }
        
        $crumb_template = $this->crumb;
        
        if (strlen($url) > 1 and substr($url, 0, 1) == '/')
        {
            $url = get_setting('base_url') . substr($url, 1);
        }
        
        $this->crumb[] = array(
                'name' => $name,
                'url' => $url
        );
        
        $crumb_template['last'] = array(
                'name' => $name,
                'url' => $url
        );
        
        TPL::assign('crumb', $crumb_template);
        
        foreach ($this->crumb as $key => $crumb)
        {
            $title = $crumb['name'] . ' - ' . MOX_APP::$settings['site_name'].'管理后台';
        }
        
        TPL::assign('page_title', htmlspecialchars(rtrim($title, ' - ')));
        
        return $this;
    }
}