<?php
class FARM_CONTROLLER
{
    public $user_id;
    public $user_info;

    public function jsonReturn($result = array(), $code = 1, $message = 'SUCCESS', $notice = null, $time = '')
    {
        if (!empty($this->user_info)) {
            $notice = FARM_APP::model("notice")->get_notice($this->user_info['uid']);
        }

        $ret = array('code' => $code, 'message' => $message, 'result' => $result, 'notice' => $notice, 'time' => $time);

        /*
        $logs = '客户端操作：（'.get_client().','.fetch_ip().'）';
        FARM_APP::model('logs')->insert('logs', array(
                                                                'content' => $logs.json_encode($notice),
                                                                'level' => 'info',
                                                                'create_time' => time()));*/

        echo json_encode($ret);
        exit;
    }
    
    public function __construct($process_setup = true)
    {
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

        // 封IP
        $ip = fetch_ip();
        $fidden_ip = array('218.18.78.95');
        if (in_array($ip, $fidden_ip) && !is_bot() && !empty($ip)) {
            exit();
        }

        if (0)
        {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
            if ($http_type == 'http') {
                $https = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                header('HTTP/1.1 301 Moved Permanently');
                header("Location: ".$https);
                exit();
            }
        }

        $user_info = FARM_APP::user()->get_info();

        $unread_msg_count = 0;
        if (!empty($user_info['uid']))
        {
            $user_info = FARM_APP::model("user") -> get_user_info_by_id($user_info['uid']);
            $user_info['uid'] = $user_info['id'];
            $unread_msg_count = FARM_APP::model("message") -> count('message', 'user_id = '.intval($user_info['uid']).' AND is_read = 0');
        }
        TPL::assign('unread_msg', intval($unread_msg_count));
        TPL::assign('user_info', $user_info);

        $this->user_info = $user_info;
        
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
        return FARM_APP::model($model);
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
 * FarmNc 后台控制器
 *
 * @package     FarmNc
 * @subpackage  System
 * @category    Libraries
 * @author      FarmNc Dev Team
 */
class FARM_ADMIN_CONTROLLER extends FARM_CONTROLLER
{
    public $per_page = 20;
    
    public function __construct()
    {
        parent::__construct(false);
        
        if ($_GET['app'] != 'admin')
        {
            return false;
        }
        
        TPL::import_clean();
        
        TPL::import_js(array(
            'js/jquery.js',
            'admin/js/aws_admin.js',
            'admin/js/aws_admin_template.js',
            'js/jquery.form.js',
            'admin/js/framework.js',
            'admin/js/global.js',
        ));
        
        TPL::import_css(array(
            'admin/css/common.css'
        ));
        
        if (in_array($_GET['act'], array(
            'login',
            'login_process',
        )))
        {
            return true;
        }
        
        if ($admin_info = H::decode_hash(FARM_APP::session()->admin_login))
        {
            if (!$admin_info['uid'])
            {
                unset(FARM_APP::session()->admin_login);
                
                if ($_POST['_post_type'] == 'ajax')
                {
                    H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('会话超时, 请重新登录')));
                }
                else
                {
                    H::redirect_msg(FARM_APP::lang()->_t('会话超时, 请重新登录'), '/admin/login/url-' . base64_encode($_SERVER['REQUEST_URI']));
                }
            }
        }
        else
        {
            if ($_POST['_post_type'] == 'ajax')
            {
                H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('会话超时, 请重新登录')));
            }
            else
            {
                HTTP::redirect('/admin/login/url-' . base64_encode($_SERVER['REQUEST_URI']));
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
            $title = $crumb['name'] . ' - ' . '模型圈运营平台';
        }
        
        TPL::assign('page_title', htmlspecialchars(rtrim($title, ' - ')));
        
        return $this;
    }
}