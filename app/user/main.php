<?php

class main extends FARM_CONTROLLER
{
    public function captcha_action()
    {
        FARM_APP::captcha()->generate();
    }

    /**
     * 用户主页
     */
    public function index_action()
    {
        TPL::import_css('css/base.css');

        TPL::assign('seo', get_seo('userindex'));

        $user_id = intval($_GET['id']);

        $user_info = $this->model('user')->get_user_info_by_id($user_id);
        TPL::assign('user', $user_info);

        $honor = $this->model('points')->getHonor($user_info['point']);
        TPL::assign('honor', $honor);

        $feed_list = $this->model('feed')-> get_data_list(array('user_id = "'.$user_id.'"'));
        TPL::assign('feed_list', $feed_list);

        // 模型圈推荐用户
        $user_list = $this->model('user')-> get_data_list(array('is_us = 1'), 1, 6, 'point desc');
        TPL::assign('user_list', $user_list);

        // 本周热门帖子
        $hot_feed = $this->model('feed')-> get_data_list(array('is_home <> 1'), 1, 15, 'read_num desc');
        TPL::assign('hot_feed', $hot_feed);

        //发送消息
        $uid = intval(FARM_APP::session()->info['uid']);
        if (!empty($uid) && ($uid != $user_id)) {
            $user = $this->model('user')->get_user_info_by_id($uid);
            $url = G_DEMAIN . '/user/' . $uid . '/';
            $this->model('message')->send($user_id, 0, '圈友 <b>' . $user['user_name'] . '</b> 拜访了您的个人主页，去看看？', $url, 'visit');
        }

        $seo = array('title' => array($user_info['user_name']),
                     'keywords' => array($user_info['user_name']),
                     'description' => array($user_info['user_name']));
        TPL::assign('seo', get_seo('user-index', $seo));

        $this->model('points')->send($user_id, 'visit_user_index');

        TPL::output('user/index');
    }

    /**
     * 用户中心
     * */
    public function home_action()
    {
        TPL::import_css('css/base.css');

        TPL::assign('seo', get_seo('usercenter'));

        $user_id = FARM_APP::session()->info['uid'];

        if (empty($user_id)) {
            HTTP::redirect(G_DEMAIN . '/user/login/');
            exit;
        }

        $honor = $this->model('points')->getHonor($this->user_info['point']);
        TPL::assign('honor', $honor);

        $feed_list = $this->model('feed')-> get_data_list(array('user_id ='.$user_id));
        TPL::assign('feed_list', $feed_list);

        TPL::assign('seo', get_seo('user-home'));

        TPL::output('user/home');
    }

    /**
     * 注册
     * */
    public function register_action()
    {
        $url = base64_decode($_GET['url']);
        $user_info = FARM_APP::user()->get_info();
        if ($user_info) {
            if ($url) {
                header('Location: ' . $url);
            } else {
                HTTP::redirect('/user/home');
            }
        }

        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');
        TPL::import_css('css/base.css');

        if ($_GET['url']) {
            $return_url = htmlspecialchars(base64_decode($_GET['url']));
        } else {
            $return_url = htmlspecialchars($_SERVER['HTTP_REFERER']);
        }

        TPL::assign('return_url', $return_url);
        TPL::assign('seo', get_seo('register'));

        TPL::output('user/register');
    }

    /**
     * 登录
     * */
    public function login_action()
    {
        $url = base64_decode($_GET['url']);
        $user_info = FARM_APP::user()->get_info();
        if ($user_info) {
            if ($url) {
                header('Location: ' . $url);
            } else {
                HTTP::redirect('/user/home');
            }
        }

        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');
        TPL::import_css('css/base.css');

        if ($_GET['url']) {
            $return_url = htmlspecialchars(base64_decode($_GET['url']));
        } else {
            $return_url = htmlspecialchars($_SERVER['HTTP_REFERER']);
        }

        TPL::assign('return_url', $return_url);

        TPL::assign('seo', get_seo('login'));

        TPL::output("user/login");
    }

    /**
     * 退出
     * */
    public function logout_action($return_url = null)
    {
        if ($_GET['return_url']) {
            $url = strip_tags(urldecode($_GET['return_url']));
        } else if (!$return_url) {
            $url = '/';
        } else {
            $url = $return_url;
        }

        $this->model('user')->logout();

        HTTP::redirect($url);
        exit;
    }
}