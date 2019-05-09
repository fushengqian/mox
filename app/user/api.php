<?php
class api extends MOX_CONTROLLER
{
    /**
     * 保存资料
     */
    public function saveinfo_action()
    {
        $user_name = trim($_POST['user_name']);
        $intro     = trim($_POST['intro']);
        $skill     = trim($_POST['skill']);
        $field     = trim($_POST['field']);
        $city      = trim($_POST['city']);
        $province  = trim($_POST['province']);

        $user_id = MOX_APP::session()->info['uid'];
        if (empty($user_id)) {
            return;
        }

        $arr = array('last_login' => time());

        if ($user_name) {
            $arr['user_name'] =  $user_name;
        }

        if ($intro) {
            $arr['intro'] = $intro;
        }

        if ($skill) {
            $arr['skill'] = $skill;
        }

        if ($field) {
            $arr['field'] = $field;
        }

        if ($city) {
            $arr['city'] = $city;
        }

        if ($province) {
            $arr['province'] = $province;
        }

        $this->model('user')->update_user_fields($arr, $user_id);

        $info = $this -> _getUserInfo($user_id, $user_id);

        $this -> jsonReturn($info);
    }

        /**
     * 上传头像
     */
    public function avatar_action()
    {
        $user_id = MOX_APP::session()->info['uid'];
        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        if (!$file = $_FILES['file']) {
            $this -> jsonReturn([], -1, '没有图片上传！');
        }

        if ($_FILES['file']['size'] > 1024 * 1024 * 10 || $_FILES['file']['size'] == 0) {
            $this -> jsonReturn([], -1, '图片大小不符合！');
        }

        $upload_path = APP_PATH . 'static/upload/' . date("Ymd", time());
        $partList = explode('/', $upload_path);

        $path = '';
        foreach ($partList as $part) {
            $path .= $part . '/';
            if (is_dir($path)) {
                continue;
            }
            if (!mkdir($path)) {
                chmod($path, 0755);
            }
        }

        $file_name = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $path_info = pathinfo('/' . $file_name . $file['name']);
        $upload_path .= '/' . $file_name . '.' . $path_info['extension'];

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            $url = '/static/upload/' . date("Ymd") . '/' . $file_name . '.' . $path_info['extension'];

            $this->model('user')->update_user_fields(array('avatar' => $url, 'last_login' => time()), $user_id);

            MOX_APP::model('points')->send($user_id, 'upload_avatar');
        }

        $info = $this -> _getUserInfo($user_id, 0);

        $this -> jsonReturn($info);
    }

    /**
     * 获取用户信息
     */
    public function info_action()
    {
        $uid = trim($_POST['id']);

        // 当前登录用户信息
        if (empty($uid)) {
            $uid = MOX_APP::session()->info['uid'];
        }

        if (!$uid) {
            $this -> jsonReturn(null, -1, '系统无法获取该用户信息！');
        }

        $user_id = MOX_APP::session()->info['uid'];

        $info = $this -> _getUserInfo($uid, $user_id);

        $this -> jsonReturn($info);
    }

    /**
     * 注册接口
     */
    public function register_action()
    {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $gender   = trim($_POST['gender']);
        $phone    = trim($_POST['phone']);

        if (empty($username) || empty($password) || empty($phone)) {
            $this -> jsonReturn(null, -1, '用户名、手机、密码不能为空！');
        }

        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
            $this -> jsonReturn(null, -1, '密码长度必须6-16！');
        }

        // 手机号不能重复
        $data = $this->model('user')->get_user_info_by_phone($phone);
        if ($data) {
            $this -> jsonReturn(null, -1, '该手机号已经存在！');
        }

        // 用户名不能重复
        $data = $this->model('user')->get_user_info_by_username($username);
        if ($data) {
            $this -> jsonReturn(null, -1, '该用户名已经存在！');
        }

        $uid = $this->model('user')->user_register($username, $password, '', intval($gender), $phone);

        if ($uid) {
            $user_info = $this->model('user')->get_user_info_by_id($uid);

            //登录
            $this->model('user')->update_user_last_login($uid);
            $this->model('user')->setcookie_login($uid, $username, $password, $user_info['salt'], null);

            // 发送消息
            $this->model('message')->send($uid, 0, '亲爱的 '.trim($username).' ：欢迎您加入我们模型圈的大家族！在遵守本站的规定的同时，享受您的愉快之旅吧！');
            MOX_APP::model('points')->send($uid, 'register');

        } else {
            $this -> jsonReturn(null, -1, '抱歉，注册失败！');
        }

        // 返回用户信息
        $user_info = $this -> _getUserInfo($user_info['id'], $user_info['id']);

        $this -> jsonReturn($user_info);
    }

    /**
     * 登录接口
     */
    public function login_action()
    {
        $account = trim($_POST['account']);
        $password = trim($_POST['password']);

        if (empty($account) || empty($password)) {
            $this -> jsonReturn(null, -1, '抱歉，系统参数有误！');
        }

        $user_info = $this->model('user')->check_login($account, $password);

        if ($user_info) {
            $user_id = $this->model('user')->get_us(intval($user_info['id']));
            if ($user_id != $user_info['id']) {
                $user_info = $this->model('user')->get_user_info_by_id($user_id);
            }

            $this->model('user')->update_user_last_login($user_info['id']);

            $this->model('user')->setcookie_login($user_info['id'], $user_info['user_name'], $password, $user_info['salt']);
        } else {
            $this -> jsonReturn(null, -1, '账号或密码有误！');
        }

        MOX_APP::model('points')->send($user_info['id'], 'login');

        // 返回用户信息
        $user_info = $this -> _getUserInfo($user_info['id'], $user_info['id']);

        $this -> jsonReturn($user_info);
    }

    /**
     * 获取用户信息
     * @param int $uid
     * @param int $my_user_id
     * @return array
     */
    private function _getUserInfo($uid, $my_user_id = 0) {
        $user_info = $this->model('user')->get_user_info_by_id($uid);

        $feed_count = $this->model('feed')->count('feed', 'user_id = "'.$uid.'"');
        $article_count = $this->model('article')->count('article', 'user_id = "'.$uid.'"');

        //1：彼此关注 2：我关注ta 3：ta关注我 4：都没有关注
        $relation = 4;
        $data1 = $this -> model('system')->fetch_row('follow', "user_id = '".$my_user_id."' and follow_user_id = '".$uid."'");
        $data2 = $this -> model('system')->fetch_row('follow', "user_id = '".$uid."' and follow_user_id = '".$my_user_id."'");
        if ($data1 && $data2) {
            $relation = 1;
        } else if($data1) {
            $relation = 2;
        } else if ($data2){
            $relation = 3;
        }

        // 粉丝数、关注数
        $fan_count = $this->model('system')->count('follow', 'follow_user_id = "'.$uid.'"');
        $follow_count = $this->model('system')->count('follow', 'user_id = "'.$uid.'"');

        $company = '未填写';
        if ($user_info['is_us']) {
            $company = '模型圈';
        }

        $info = array('id' => $user_info['id'],
                     'name' => $user_info['user_name'],
                     'portrait' => G_DEMAIN.$user_info['avatar'],
                     'gender' => $user_info['sex'],
                     'desc' => $user_info['intro'],
                     'relation' => $relation,
                     'identity' => array('officialMember' => false, 'softwareAuthor' => false),
                     'statistics' => array('honorScore' => $user_info['login_num'],
                                            'activeScore' => $user_info['point'],
                                            'score' => $user_info['point'],
                                            'tweet' => $feed_count,
                                            'collect' => 0,
                                            'fans' => $fan_count,
                                            'follow' => $follow_count,
                                            'blog' => $article_count,
                                            'answer' => 0,
                                            'discuss' => 0),
                     'more' => array('joinDate' => date("Y-m-d", $user_info['reg_time']),
                                     'city' => $user_info['city'],
                                     'province' => $user_info['province'],
                                     'expertise' => $user_info['skill'],
                                     'platform' => $user_info['field'],
                                     'company' => $company,
                                     'position' => '未填写'));

        return $info;
    }
}
