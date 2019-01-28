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
        $result = array('id' => 1000,
                        'name' => 'zach',
                        'portrait' => 'http://www.moxquan.com/static/avatar/3.gif',
                        'gender' => 1,
                        'relation' => 4,
                        'identity' => array());

        $this -> jsonReturn($result);
    }
}
