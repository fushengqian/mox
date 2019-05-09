<?php
class setting extends MOX_CONTROLLER
{
    public function setup()
    {
        parent::setup();
        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');
    }

    /**
     * 账号设置
     * */
    public function info_action()
    {
        $user_info = MOX_APP::user()->get_info();

        $honor = $this->model('points')->getHonor($user_info['point']);
        TPL::assign('honor', $honor);

        if (!$user_info) {
            HTTP::redirect('/user/home');
        }

        $user_info = $this->model('user')->get_user_info_by_id($user_info['uid']);
        TPL::assign('user', $user_info);

        TPL::assign('active', 'setting');

        TPL::output("user/setting/base");
    }

    /**
     * 头像设置
     * */
    public function avatar_action()
    {
        $user_info = MOX_APP::user()->get_info();

        $honor = $this->model('points')->getHonor($user_info['point']);
        TPL::assign('honor', $honor);

        if (!$user_info) {
            HTTP::redirect('/user/home');
        }

        TPL::assign('active', 'avatar');

        TPL::output("user/setting/avatar");
    }

    /**
     * 修改密码
     * */
    public function password_action()
    {
        $user_info = MOX_APP::user()->get_info();

        $honor = $this->model('points')->getHonor($user_info['point']);
        TPL::assign('honor', $honor);

        if (!$user_info) {
            HTTP::redirect('/user/home');
        }

        TPL::assign('active', 'password');

        TPL::output("user/setting/pwd");
    }

    /**
     * 上传头像
     */

}
