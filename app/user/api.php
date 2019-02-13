<?php
class api extends FARM_CONTROLLER
{
    /**
     * 获取用户信息
     */
    public function info_action()
    {
        $uid = trim($_POST['id']);
        if (!$uid) {
            $this -> jsonReturn(null, -1, '系统无法获取该用户信息！');
        }

        $user_info = $this->model('user')->get_user_info_by_id($uid);

        $feed_count = $this->model('feed')->count('feed', 'user_id = "'.$uid.'"');
        $article_count = $this->model('article')->count('article', 'user_id = "'.$uid.'"');

        $info = array('id' => $user_info['id'],
                      'name' => $user_info['user_name'],
                      'portrait' => G_DEMAIN.$user_info['avatar'],
                      'gender' => $user_info['sex'],
                      'desc' => $user_info['intro'],
                      'relation' => 4,
                      'identity' => array('officialMember' => false, 'softwareAuthor' => false),
                      'statistics' => array('honorScore' => $user_info['login_num'],
                                            'activeScore' => $user_info['point'],
                                            'score' => $user_info['point'],
                                            'tweet' => $feed_count,
                                            'collect' => 0,
                                            'fans' => 108,
                                            'follow' => 59,
                                            'blog' => $article_count,
                                            'answer' => 0,
                                            'discuss' => 0));

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
            FARM_APP::model('points')->send($uid, 'register');

        } else {
            $this -> jsonReturn(null, -1, '抱歉，注册失败！');
        }

        // 返回用户信息
        $result = array('id' => $user_info['id'],
                        'name' => !empty($user_info['user_name']) ? $user_info['user_name'] : $user_info['phone'],
                        'portrait' => G_DEMAIN.$user_info['avatar'],
                        'gender' => $user_info['sex'],
                        'desc' => $user_info['intro'],
                        'relation' => 4,
                        'identity' => array('officialMember' => false, 'softwareAuthor' => false));

        $this -> jsonReturn($result);
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

        FARM_APP::model('points')->send($user_info['id'], 'login');

        // 返回用户信息
        $result = array('id' => $user_info['id'],
                        'name' => !empty($user_info['user_name']) ? $user_info['user_name'] : $user_info['phone'],
                        'portrait' => G_DEMAIN.$user_info['avatar'],
                        'gender' => $user_info['sex'],
                        'desc' => $user_info['intro'],
                        'relation' => 4,
                        'identity' => array('officialMember' => false, 'softwareAuthor' => false));

        $this -> jsonReturn($result);
    }
}
