<?php
define('IN_AJAX', TRUE);

class ajax extends FARM_CONTROLLER
{
    public function check_username_action()
    {
        if ($this->model('user')->check_username_char($_POST['username'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('用户名不符合规则')));
        }

        if ($this->model('user')->check_username_sensitive_words($_POST['username']) || $this->model('user')->check_username($_POST['username'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('用户名已被注册')));
        }

        H::ajax_json_output(FARM_APP::RSM(null, 1, null));
    }

    public function register_process_action()
    {
        if (trim($_POST['phone']) == '') {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('请输入手机号')));
        } else if ($this->model('user')->check_phone($_POST['phone'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('手机号已经存在')));
        } else if (!H::valid_mobile($_POST['phone'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('手机号格式有误')));
        }

        if (!empty($_POST['email']) && $this->model('user')->check_email($_POST['email'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('E-Mail 已经被使用, 或格式不正确')));
        }

        if (empty($_POST['user_name'])) {
            $_POST['user_name'] = $_POST['mobile'];
        }

        if (!empty($_POST['user_name']) && $this->model('user')->check_username($_POST['user_name'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('用户名已经存在')));
        } else if ($check_rs = $this->model('user')->check_username_char($_POST['user_name'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('用户名包含无效字符')));
        } else if ($this->model('user')->check_username_sensitive_words($_POST['user_name']) OR trim($_POST['user_name']) != $_POST['user_name']) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('用户名中包含敏感词或系统保留字')));
        }

        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('密码长度不符合规则')));
        }

        // 检查验证码
        if (!FARM_APP::captcha()->is_validate($_POST['seccode_verify'])) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('请填写正确的验证码')));
        }

        $uid = $this->model('user')->user_register($_POST['user_name'], $_POST['password'], $_POST['email'], intval($_POST['sex']), $_POST['phone']);

        $this->model('user')->setcookie_logout();
        $this->model('user')->setsession_logout();

        if ($uid) {
            $user_info = $this->model('user')->get_user_info_by_id($uid);

            if (!empty($_POST['return_url'])) {
                $this->model('user')->setcookie_login($user_info['id'], $user_info['user_name'], $_POST['password'], $user_info['salt']);
                $return_url = strip_tags($_POST['return_url']);
            } else {
                $return_url = get_js_url('/home/');
            }

            //登录
            $this->model('user')->update_user_last_login($uid);
            $this->model('user')->setcookie_login($uid, $_POST['user_name'], $_POST['password'], $user_info['salt'], null);

            // 发送消息
            $this->model('message')->send($uid, 0, '亲爱的 '.trim($_POST['user_name']).' ：欢迎您加入我们模型圈的大家族！在遵守本站的规定的同时，享受您的愉快之旅吧！');

            FARM_APP::model('points')->send($uid, 'register');

            H::ajax_json_output(FARM_APP::RSM(array(
                'url' => $return_url
            ), 1, '恭喜，注册成功'));
        } else {
            H::ajax_json_output(FARM_APP::RSM(null, -1, '注册失败'));
        }
    }

    public function login_process_action()
    {
        if (empty($_POST['user_name'])) {
            $_POST['user_name'] = $_POST['phone'];
        }

        //通过验证码登录
        $hash_password = true;
        if ($_POST['type'] == 'vcode') {
            $user_info = $this->model('user')->login_by_vcode($_POST['mobile'], $_POST['vcode']);
            if (!$user_info) {
                H::ajax_json_output(FARM_APP::RSM(null, -1, '您的手机验证码有误！'));
            }
            $_POST['password'] = $user_info['password'];
            $hash_password = false;
        } else {
            $user_info = $this->model('user')->check_login($_POST['phone'], $_POST['password']);
            if (!$user_info) {
                H::ajax_json_output(FARM_APP::RSM(null, -1, '请输入正确的帐号或密码'));
            }
        }

        $user_id = $this->model('user')->get_us(intval($user_info['id']));
        if ($user_id != $user_info['id']) {
            $user_info = $this->model('user')->get_user_info_by_id($user_id);
        }

        if ($user_info) {
            if ($_POST['net_auto_login']) {
                $expire = 60 * 60 * 24 * 360;
            }

            $this->model('user')->update_user_last_login($user_info['id']);
            $this->model('user')->setcookie_logout();
            $this->model('user')->setcookie_login($user_info['id'], $_POST['user_name'], $_POST['password'], $user_info['salt'], $expire, $hash_password);

            FARM_APP::model('points')->send($user_info['id'], 'login');

            H::ajax_json_output(FARM_APP::RSM(array(
                'url' => get_js_url('/home/'),
            ), 1, '登录成功！'));
        }
    }

    public function modify_password_action()
    {
        $user_id = FARM_APP::session()->info['uid'];

        if (!$user_id) {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请先登录')));
        }

        if (!$_POST['old_password']) {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请输入当前密码')));
        }

        if ($_POST['password'] != $_POST['re_password']) {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请输入相同的确认密码')));
        }

        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('密码长度不符合规则')));
        }

        $user_info = $this->model('user')->get_user_info_by_id($user_id);

        if ($this->model('user')->update_user_password($_POST['old_password'], $_POST['password'], $user_id, $user_info['salt'])) {
            H::ajax_json_output(FARM_APP::RSM(null, 1, FARM_APP::lang()->_t('密码修改成功, 请牢记新密码')));
        } else {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请输入正确的当前密码')));
        }
    }

    /**
     * 保存资料
     */
    public function saveinfo_action()
    {
        $user_name = $_POST['user_name'];
        $intro     = $_POST['intro'];
        $sex       = intval(['sex']);

        $user_id = FARM_APP::session()->info['uid'];

        if (!$user_name) {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请输入昵称')));
        }

        if (strlen($_POST['user_name']) < 4 OR strlen($_POST['user_name']) > 30) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('昵称长度不符合规则')));
        }

        $user = $this->model('user')->get_user_info_by_username($user_name);
        if ($user && ($user['id'] != $user_id)) {
            H::ajax_json_output(FARM_APP::RSM(null, -1, FARM_APP::lang()->_t('该昵称已经被占用')));
        }

        $result = $this->model('user')->update_user_fields(array('user_name' => $user_name, 'sex' => $sex, 'intro' => $intro, 'last_login' => time()), $user_id);

        if ($result) {
            FARM_APP::model('points')->send($user_id, 'update_user_info');
            H::ajax_json_output(FARM_APP::RSM(null, 1, FARM_APP::lang()->_t('修改成功！')));
        } else {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('抱歉，修改失败！')));
        }
    }

    /**
     * 保存头像
     */
    public function save_avatar_action()
    {
        $avatar = trim($_POST['avatar']);

        $avatar = str_replace(G_DEMAIN, '', $avatar);

        $user_id = FARM_APP::session()->info['uid'];

        if (!$avatar) {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('请上传头像')));
        }

        $result = $this->model('user')->update_user_fields(array('avatar' => $avatar, 'last_login' => time()), $user_id);

        if ($result) {
            FARM_APP::model('points')->send($user_id, 'upload_avatar');

            H::ajax_json_output(FARM_APP::RSM(null, 1, FARM_APP::lang()->_t('修改成功！')));
        } else {
            H::ajax_json_output(FARM_APP::RSM(null, '-1', FARM_APP::lang()->_t('抱歉，修改失败！')));
        }
    }
}
