<?php
class api extends FARM_CONTROLLER
{
    /**
     * 注册接口
     */
    public function register_action()
    {
        // empty
    }

    /**
     * 登录接口
     */
    public function login_action()
    {
        $account = trim($_POST['account']);
        $password = trim($_POST['password']);

        if (empty($account) || empty($password)) {
            $this -> jsonReturn(null, 0, '抱歉，系统参数有误！');
        }

        // 设置一个cookie
        HTTP::set_cookie('user_id', md5('zach1000'), null, '/', null, false, true);

        // 返回用户信息
        $result = array('id' => 1000,
                        'name' => 'zach',
                        'portrait' => 'http://www.moxquan.com/static/avatar/3.gif',
                        'gender' => 1,
                        'relation' => 4,
                        'identity' => array('officialMember' => true, 'softwareAuthor' => true));

        $this -> jsonReturn($result);
    }
}
