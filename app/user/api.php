<?php
define('IN_AJAX', TRUE);

class api extends FARM_CONTROLLER
{
    public function register_action()
    {
        // empty
    }

    public function login_action()
    {
        $result = array('code' => 1,
                        'message' => '请求成功',
                        $arr = array('id' => 1000,
                                      'name' => 'zach',
                                      'portrait' => 'http://www.moxquan.com/static/avatar/3.gif',
                                      'gender' => 1,
                                      'relation' => 4,
                                      'identity' => array()));

        echo json_encode($result);
        exit();
    }
}
